<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InvoiceCreated;

class AdminKtaController extends Controller
{
    public function index(Request $r)
    {
        $query = User::query()->whereNotNull('membership_card_number');

        // Filter search keyword
        if($search = $r->get('q')){
            $query->where(function($q) use ($search){
                $q->where('name','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('membership_card_number','like','%'.$search.'%');
            });
        }

        // Filter bulan terbit
        if($issuedMonth = $r->get('issued_month')){
            $query->whereMonth('membership_card_issued_at', $issuedMonth);
        }

        // Filter bulan expired
        if($expireMonth = $r->get('expire_month')){
            $query->whereMonth('membership_card_expires_at', $expireMonth);
        }

        // Filter status
        if($status = $r->get('status')){
            if($status === 'active'){
                $query->where('membership_card_expires_at', '>=', now());
            } elseif($status === 'expired'){
                $query->where('membership_card_expires_at', '<', now());
            }
        }

        // Eager load companies
        $users = $query->with(['companies' => function($q){
            $q->select('companies.id','name');
        }])->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.kta.index', compact('users'));
    }


    public function show(User $user, Request $r)
    {
        if(!$user->membership_card_number){
            return back()->with('error','User belum memiliki KTA.');
        }
        $company = $user->companies()->first();
        $logo = \App\Models\Setting::getValue('site_logo_path');
        $signature = \App\Models\Setting::getValue('signature_path');
        $publicNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        $validationUrl = route('kta.public',[ 'user'=>$user->id, 'number'=>$publicNumber ]);
        $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($validationUrl);
        $qrPngData = QrCode::format('png')->size(360)->margin(0)->generate($validationUrl);
        $qrPngBase64 = base64_encode($qrPngData);
        // Always use full mode for admin preview (light mode with complete info)
        return view('admin.kta.show',[ 'user'=>$user,'company'=>$company,'logo'=>$logo,'signature'=>$signature,'qrSvg'=>$qrSvg,'qrPng'=>$qrPngBase64,'validationUrl'=>$validationUrl ]);
    }

    public function pdf(User $user, Request $r)
    {
        if(!$user->membership_card_number){
            return back()->with('error','User belum memiliki KTA.');
        }
        
        // Register Arial font to DOMPDF
        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }
        
        // Copy Arial font files to storage/fonts if not exists
        $arialFont = public_path('font/arial/ARIAL.TTF');
        $arialBoldFont = public_path('font/arial/ARIALBD.TTF');
        $storageArialFont = storage_path('fonts/arial.ttf');
        $storageArialBoldFont = storage_path('fonts/arial_bold.ttf');
        
        if (file_exists($arialFont) && !file_exists($storageArialFont)) {
            copy($arialFont, $storageArialFont);
        }
        if (file_exists($arialBoldFont) && !file_exists($storageArialBoldFont)) {
            copy($arialBoldFont, $storageArialBoldFont);
        }
        
        $publicNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        $validationUrl = route('kta.public',[ 'user'=>$user->id, 'number'=>$publicNumber ]);
        $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($validationUrl);
        $qrPngData = QrCode::format('png')->size(360)->margin(0)->generate($validationUrl);
        $qrPngBase64 = base64_encode($qrPngData);
        $logo = \App\Models\Setting::getValue('site_logo_path');
        $signature = \App\Models\Setting::getValue('signature_path');
        $full = $r->boolean('full');
        
        $pdf = Pdf::loadView('kta.pdf',[ 'user'=>$user,'qrSvg'=>$qrSvg,'qrPng'=>$qrPngBase64,'validationUrl'=>$validationUrl,'logo'=>$logo,'signature'=>$signature,'full'=>$full ])->setPaper('a4','landscape');
        $safeNumber = str_replace(['/', '\\'], '-', $user->membership_card_number);
        return $pdf->download('KTA-'.$safeNumber.($full?'-full':'-plain').'.pdf');
    }

    public function renew(User $user)
    {
        if(!$user->membership_card_number){
            return back()->with('error','User belum memiliki KTA untuk diperpanjang.');
        }

        // Check if there's already a pending renewal invoice
        $existingPending = \App\Models\Invoice::where('user_id', $user->id)
            ->where('type', 'renewal')
            ->whereIn('status', [\App\Models\Invoice::STATUS_UNPAID, \App\Models\Invoice::STATUS_AWAITING])
            ->exists();
        
        if($existingPending){
            return back()->with('error', 'User masih memiliki invoice perpanjangan yang menunggu pembayaran/verifikasi.');
        }

        $currentExpiry = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
        $newExpiry = ($currentExpiry && $currentExpiry->greaterThan(now()))
            ? $currentExpiry->clone()->addYear()
            : now()->addYear();

        // Get company and payment rate based on kualifikasi
        $company = $user->companies()->first();
        
        if(!$company){
            return back()->with('error', "User {$user->name} belum memiliki data perusahaan. Silakan lengkapi data perusahaan terlebih dahulu.");
        }
        
        $rate = null;
        // Clean up whitespace
        $jenis = trim($company->jenis ?? '');
        $kualifikasi = trim($company->kualifikasi ?? '');
        
        // Validate required fields
        if(!$jenis || !$kualifikasi){
            return back()->with('error', "Data perusahaan {$company->name} belum lengkap. Jenis: ".($jenis ?: 'Kosong').", Kualifikasi: ".($kualifikasi ?: 'Kosong').". Silakan lengkapi data jenis dan kualifikasi perusahaan di menu Perusahaan.");
        }
        
        // Try exact match first (both jenis and kualifikasi must exist)
        $rate = \App\Models\RenewalPaymentRate::where('jenis', $jenis)
            ->where('kualifikasi', $kualifikasi)
            ->first();
        
        // If no exact match, try fuzzy match on kualifikasi
        if(!$rate){
            $rate = \App\Models\RenewalPaymentRate::where('kualifikasi', 'LIKE', '%'.$kualifikasi.'%')
                ->first();
        }
        
        // If still no match, try match by jenis only (take first available)
        if(!$rate){
            $rate = \App\Models\RenewalPaymentRate::where('jenis', $jenis)
                ->first();
        }
        
        if(!$rate){
            return back()->with('error', "Tarif perpanjangan tidak ditemukan untuk Jenis: {$jenis}, Kualifikasi: {$kualifikasi}. Silakan tambahkan tarif perpanjangan di menu Pengaturan atau sesuaikan data perusahaan agar cocok dengan tarif yang tersedia.");
        }
        
        $amount = $rate->amount;

        \DB::beginTransaction();
        try {
            // Create renewal invoice
            $invoice = \App\Models\Invoice::create([
                'number' => \App\Models\Invoice::generateNumber(),
                'user_id' => $user->id,
                'company_id' => $company?->id,
                'type' => 'renewal',
                'amount' => $amount,
                'currency' => 'IDR',
                'issued_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => \App\Models\Invoice::STATUS_UNPAID,
            ]);

            // Create renewal record
            \App\Models\KtaRenewal::create([
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'previous_expires_at' => $currentExpiry,
                'new_expires_at' => $newExpiry,
                'amount' => $amount,
            ]);

            \DB::commit();
            
            // Send email notification to user
            try {
                Mail::to($user->email)->queue(new InvoiceCreated($invoice));
            } catch(\Throwable $ex){
                Log::error('Failed to send renewal invoice email: '.$ex->getMessage());
            }
            
            return redirect()->route('admin.kta.index')
                ->with('success', "Invoice perpanjangan berhasil dibuat untuk {$user->name}. Invoice No: {$invoice->number} - Total: Rp ".number_format($amount, 0, ',', '.').". Email notifikasi telah dikirim ke user.");
                
        } catch(\Throwable $e){
            \DB::rollBack();
            return back()->with('error', 'Gagal membuat invoice perpanjangan: '.$e->getMessage());
        }
    }
}
