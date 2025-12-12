<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyPlant;
use Illuminate\Support\Facades\Auth;

class CompanyPlantController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Perusahaan tidak ditemukan');
        }

        $plants = $company->plants()->latest()->paginate(10);
        return view('company.plants.index', compact('company', 'plants'));
    }

    public function create()
    {
        $user = Auth::user();
        $company = $user->companies()->first();

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Perusahaan tidak ditemukan');
        }

        return view('company.plants.create', compact('company'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies()->first();

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Perusahaan tidak ditemukan');
        }

        $data = $request->validate([
            'type' => ['required', 'in:AMP,CBP'],
            'address' => ['required', 'string', 'max:500']
        ], [
            'type.required' => 'Jenis lokasi wajib diisi',
            'type.in' => 'Jenis lokasi tidak valid',
            'address.required' => 'Alamat wajib diisi',
            'address.max' => 'Alamat maksimal 500 karakter'
        ]);

        $company->plants()->create($data);

        return redirect()->route('company.plants.index')
            ->with('success', 'Lokasi ' . ($data['type'] === 'AMP' ? 'Asphalt Mixing Plant' : 'Concrete Batching Plant') . ' berhasil ditambahkan');
    }

    public function edit(CompanyPlant $plant)
    {
        $user = Auth::user();
        $company = $user->companies()->first();

        if (!$company || $plant->company_id !== $company->id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses');
        }

        return view('company.plants.edit', compact('company', 'plant'));
    }

    public function update(Request $request, CompanyPlant $plant)
    {
        $user = Auth::user();
        $company = $user->companies()->first();

        if (!$company || $plant->company_id !== $company->id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses');
        }

        $data = $request->validate([
            'type' => ['required', 'in:AMP,CBP'],
            'address' => ['required', 'string', 'max:500']
        ]);

        $plant->update($data);

        return redirect()->route('company.plants.index')
            ->with('success', 'Lokasi berhasil diperbarui');
    }

    public function destroy(CompanyPlant $plant)
    {
        $user = Auth::user();
        $company = $user->companies()->first();

        if (!$company || $plant->company_id !== $company->id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses');
        }

        $plant->delete();

        return redirect()->route('company.plants.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }
}
