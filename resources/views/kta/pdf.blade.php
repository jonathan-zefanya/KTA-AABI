@php
    $company = $user->companies()->first();
    $isPreview = isset($preview) && $preview;

    // Get template path from settings
    $templatePath = \App\Models\Setting::getValue('kta_template_path', 'img/kta_template.png');

    // Determine the full path (support uploads stored on public disk)
    if (str_starts_with($templatePath, 'uploads/')) {
        $bgPath = storage_path('app/public/' . $templatePath);
    } else {
        $bgPath = public_path($templatePath);
    }

    // Get back template path (kta_belakang.jpg)
    $backTemplatePath = public_path('img/kta_belakang.jpg');
    
    // Check if file exists, if not check in storage
    if (!file_exists($backTemplatePath)) {
        $backTemplatePath = storage_path('app/public/img/kta_belakang.jpg');
    }
    
    // Check if back template is set in settings, otherwise use default
    $backTemplatePathSetting = \App\Models\Setting::getValue('kta_back_template_path', null);
    if ($backTemplatePathSetting) {
        if (str_starts_with($backTemplatePathSetting, 'uploads/')) {
            $backTemplatePath = storage_path('app/public/' . $backTemplatePathSetting);
        } else {
            $backTemplatePath = public_path($backTemplatePathSetting);
        }
    }

    // Convert background image to base64 for PDF embedding
    $bgBase64 = '';
    if ($bgPath && file_exists($bgPath)) {
        $imageData = file_get_contents($bgPath);
        $mime = mime_content_type($bgPath) ?: 'image/png';
        $bgBase64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }

    // Convert back template to base64
    $backBase64 = '';
    if ($backTemplatePath && file_exists($backTemplatePath)) {
        $imageData = file_get_contents($backTemplatePath);
        $mime = mime_content_type($backTemplatePath) ?: 'image/jpeg';
        $backBase64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }

    // Get layout configuration
    $layoutConfig = json_decode(\App\Models\Setting::getValue('kta_layout_config', '{}'), true);
    $cfg = [
        'member_box' => $layoutConfig['member_box'] ?? ['left' => 1.3, 'top' => 1.2, 'fontSize' => 14],
        'title' => $layoutConfig['title'] ?? ['left' => 11.5, 'top' => 4.2, 'fontSize' => 16],
        'meta' => $layoutConfig['meta'] ?? ['left' => 7.5, 'top' => 6.5, 'width' => 16, 'fontSize' => 12, 'labelWidth' => 6],
        'expiry' => $layoutConfig['expiry'] ?? ['left' => 10.5, 'top' => 15.8, 'fontSize' => 11],
        'photo' => $layoutConfig['photo'] ?? ['left' => 7.3, 'top' => 14.5, 'width' => 3.5, 'height' => 4.8],
        'qr' => $layoutConfig['qr'] ?? ['right' => 1, 'bottom' => 1.8, 'width' => 3.5, 'height' => 3.5],
    ];

    // Prepare member/company photo as base64 (embed from storage)
    $photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null);
    $photoBase64 = '';
    if ($photo) {
        $photoPath = storage_path('app/public/' . ltrim($photo, '/'));
        if (!file_exists($photoPath)) {
            // Try public storage path
            $photoPath = public_path('storage/' . ltrim($photo, '/'));
        }
        if ($photoPath && file_exists($photoPath)) {
            $pData = file_get_contents($photoPath);
            $pMime = mime_content_type($photoPath) ?: 'image/jpeg';
            $photoBase64 = 'data:' . $pMime . ';base64,' . base64_encode($pData);
        }
    }

    // Get plant addresses
    $ampAddresses = $company ? $company->ampAddresses()->get() : [];
    $cbpAddresses = $company ? $company->cbpAddresses()->get() : [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>KTA {{ $user->membership_card_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
        font-family: 'Arial', sans-serif; 
        @if($isPreview)
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 20px;
        @else
        background: #fff;
        @endif
    }
    /* Make PDF exactly the required physical size (cm) */
    @page { size: 29.7cm 21.28cm; margin: 0; page-break-after: always; }

    .page{
        position:relative;
        background-image: url('{{ $backBase64 }}');
        background-size: cover;
        background-position: center;
        width:29.7cm;
        height:21.28cm;
        margin:0 auto;
        page-break-after: always;
        @if($isPreview)
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
        @endif
    }
    .bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .layer{position:absolute;inset:0;}

    /* Nomor Anggota */
    .member-box{
        position:absolute;
        left:2%;
        top:1.8cm;
        width:5.25cm;
        padding:0.3cm;
        font-weight:700;
        font-size:21px;
        letter-spacing:1px;
        text-align:center;
        /* background:#fff; */
        z-index:10;
    }

    /* Judul KARTU TANDA ANGGOTA */
    .title{
        position:absolute;
        top:4.4cm;
        left:55%;
        font-weight:800;
        font-size:14px;
        text-decoration:underline;
        z-index:10;
    }

    /* Data perusahaan - table format */
    .meta{
        position:absolute;
        left:40%;
        top:5.2cm;
        width:23cm;
        font-size:11px;
        line-height:1.3;
        z-index:5;
    }
    .meta table {
        border-collapse: collapse;
        width: 100%;
        border: none;
        /* buat agak ke kiri */
        margin-left: -2.5cm;
    }
    .meta table td {
        border: none;
        padding: 0.25cm 0.2cm;
        vertical-align: top;
        line-height: 1.5;
        font-size:15px;
    }
    .meta table td:first-child {
        font-weight: 700;
        width: 4cm;
        white-space: nowrap;
    }
    .meta table td:nth-child(2) {
        width: 0.4cm;
        text-align: center;
    }
    .meta table td:last-child {
        word-break: break-word;
        font-size:15px;
    }

    /* Pas Foto */
    .photo{
        position:absolute;
        left:26%;
        top:70%;
        width:3.2cm;
        height:4.2cm;
        /* max-width: 3.8cm; */
        /* max-height: 5.2cm; */
        /* border:2px solid #000; */
        overflow:hidden;
        /* background:#eee; */
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:10;
    }
    .photo img{
        width:100%;
        height:100%;
        object-fit:cover;
        object-position:center;
    }

    /* Photo Label */
    .photo-label{
        position:absolute;
        left:25%;
        top:78%;
        width:3.8cm;
        font-size:9px;
        font-weight:600;
        text-align:center;
        line-height:1.3;
        z-index:10;
    }

    /* QR Code - Left side */
    .qr{
        position:absolute;
        left:1cm;
        top:70%;
        width:3.8cm;
        height:3.8cm;;
        padding: 0.4cm;
        display:flex;
        align-items:center;
        justify-content:center; 
        z-index:10;
    }
    .qr-inner {
        position: absolute;
        top:0.25cm;
        left:0.25cm;
        right:0.25cm;
        bottom:0.25cm;
    }
    .qr img, 
    .qr svg{
        max-width:100%;
        max-height:100%;
        margin: 0.2cm;
        object-fit:contain;
    }

    /* Bar masa berlaku */
    .expiry{
        position:absolute;
        left:33%;
        top:14.5cm;
        width:18cm;
        padding:0.3cm 0.4cm;
        /* background:#fff; */
        font-weight:700;
        font-size:15px;
        line-height:1.4;
        text-align:center;
        z-index:10;
    }
</style>

</head>
<body>
<div class="page">
    @if($bgBase64)
        <img class="bg" src="{{ $bgBase64 }}" alt="bg">
    @endif
    <div class="layer">
        <!-- Nomor Anggota -->
        @if($user->membership_card_number)
        <div class="member-box">{{ $user->membership_card_number }}</div>
        @endif

        <!-- Judul -->
        <div class="title">KARTU TANDA ANGGOTA</div>

        <!-- Data Perusahaan -->
        @if($company)
        <div class="meta">
            <table>
                <tr>
                    <td>NAMA PERUSAHAAN</td>
                    <td>:</td>
                    <td>{{ $company->name }}</td>
                </tr>
                <tr>
                    <td>NAMA PIMPINAN</td>
                    <td>:</td>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <td>NO. NPWP</td>
                    <td>:</td>
                    <td>{{ $company->npwp ?? '-' }}</td>
                </tr>
                <tr>
                    <td>NO. NIB</td>
                    <td>:</td>
                    <td>{{ $company->nib ?? '-' }}</td>
                </tr>
                <tr>
                    <td>KUALIFIKASI</td>
                    <td>:</td>
                    <td>{{ $company->kualifikasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td>ALAMAT PERUSAHAAN</td>
                    <td>:</td>
                    <td>{{ $company->address ?? '-' }}<br>
                        {{ $company->city_name ?? 'Kab/Kota: -' }}, Provinsi: {{ $company->province_name ?? '-' }}<br>
                        <b>No. Telp:</b> {{ $company->phone ?? '-' }}, <b>Email:</b> {{ $company->email ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Pas Foto -->
        @if($photoBase64)
            <div class="photo">
                <img src="{{ $photoBase64 }}" alt="Foto">
            </div>
        @endif

        <!-- Photo Label -->
        {{-- <div class="photo-label">FOTO<br>Stempel DPP</div> --}}

        <!-- QR Code -->
        <div class="qr">
            @if(isset($qrPng) && !empty($qrPng))
                <img src="data:image/png;base64,{{ $qrPng }}" alt="QR">
            @elseif(isset($qrSvg) && !empty($qrSvg))
                {!! $qrSvg !!}
            @else
                <div style="font-size:9px;font-weight:600;color:#999;">QR Code</div>
            @endif
        </div>

        <!-- Masa Berlaku -->
        <div class="expiry">
            BERLAKU SAMPAI DENGAN TANGGAL<br>{{ optional($user->membership_card_expires_at)->format('d F Y') }}
        </div>
    </div>
</div>

<!-- HALAMAN KEDUA - DAFTAR AMP & CBP -->
<div class="page">
    @if($backBase64)
        <img class="bg" src="{{ $backBase64 }}" alt="bg-back">
    @endif
    <div class="layer">
        <!-- Lokasi AMP Section -->
        <div style="position:absolute;top:5.5cm;left:1.5cm;right:1.5cm;">
            <div style="font-weight:700;font-size:15px;margin-bottom:0.3cm;">Lokasi <i>Asphalt Mixing Plant</i></div>
            @if($ampAddresses->count())
            @foreach($ampAddresses as $idx => $plant)
            <div style="font-size:15px;margin:0.15cm 0;line-height:1.4;">
                <span style="font-weight:600;">{{ $idx + 1 }}.</span> {{ $plant->address }}
            </div>
            @endforeach
            @else
            <div style="font-size:15px;color:#999;margin:0.15cm 0;">Tidak ada data AMP terdaftar</div>
            @endif
        </div>

        <!-- Lokasi CBP Section -->
        <div style="position:absolute;top:9cm;left:1.5cm;right:1.5cm;">
            <div style="font-weight:700;font-size:15px;margin-bottom:0.3cm;">Lokasi <i>Concrete Batching Plant</i></div>
            @if($cbpAddresses->count())
            @foreach($cbpAddresses as $idx => $plant)
            <div style="font-size:15px;margin:0.15cm 0;line-height:1.4;">
                <span style="font-weight:600;">{{ $idx + 1 }}.</span> {{ $plant->address }}
            </div>
            @endforeach
            @else
            <div style="font-size:15px;color:#999;margin:0.15cm 0;">Tidak ada data CBP terdaftar</div>
            @endif
        </div>
        </div>
    </div>
</div>
</body>
</html>
