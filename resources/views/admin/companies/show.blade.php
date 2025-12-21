@extends('admin.layout')
@section('title','Detail Perusahaan')
@section('page_title','Detail Perusahaan')
@section('content')
<div class="adm-card mb-4">
    <div class="d-flex justify-content-between mb-2">
        <h5 class="mb-0 text-light">{{ $company->name }}</h5>
        <div class="small d-flex gap-2">
            <a href="{{ route('admin.companies.edit',$company) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            <a href="{{ route('admin.companies.downloadAll',$company) }}" class="btn btn-sm btn-outline-secondary">Download ZIP</a>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </div>
    <div class="row small g-3">
        <div class="col-md-6">
            <div class="text-dim">Jenis / Kualifikasi</div>
            <div>{{ $company->jenis ?? '-' }} / {{ $company->kualifikasi ?? '-' }}</div>
        </div>
        <div class="col-md-6">
            <div class="text-dim">PJBU</div>
            <div>{{ $company->penanggung_jawab ?? '-' }}</div>
        </div>
        <div class="col-md-4">
            <div class="text-dim">NPWP</div>
            <div>{{ $company->npwp ?? '-' }}</div>
        </div>
        <div class="col-md-4">
            <div class="text-dim">NIB</div>
            <div>{{ $company->nib ?? '-' }}</div>
        </div>
        <div class="col-md-4">
            <div class="text-dim">Wilayah</div>
            <div>{{ $company->city_name }}, {{ $company->province_name }} {{ $company->postal_code }}</div>
        </div>
        <div class="col-12">
            <div class="text-dim">Alamat</div>
            <div>{{ $company->address }}</div>
        </div>
    </div>
    <hr class="border-secondary my-4">
    <h6 class="mb-2">Dokumen</h6>
    <ul class="small mb-0" style="columns:2; -webkit-columns:2; -moz-columns:2;">
        <li>Foto PJBU: @if($company->photo_pjbu_path)<a target="_blank" href="{{ asset('storage/'.$company->photo_pjbu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
        <li>NPWP BU: @if($company->npwp_bu_path)<a target="_blank" href="{{ asset('storage/'.$company->npwp_bu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
        <li>AKTE BU: @if($company->akte_bu_path)<a target="_blank" href="{{ asset('storage/'.$company->akte_bu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
        <li>NIB: @if($company->nib_file_path)<a target="_blank" href="{{ asset('storage/'.$company->nib_file_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
        <li>KTP PJBU: @if($company->ktp_pjbu_path)<a target="_blank" href="{{ asset('storage/'.$company->ktp_pjbu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
        <li>NPWP PJBU: @if($company->npwp_pjbu_path)<a target="_blank" href="{{ asset('storage/'.$company->npwp_pjbu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
    </ul>
    <hr class="border-secondary my-4">
    <h6 class="mb-2">Lokasi Plant (AMP & CBP)</h6>
    @php
        $plants = $company->plants;
    @endphp
    @if($plants->count())
        <div class="table-responsive">
            <table class="table table-sm table-dark table-striped small">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Alamat</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plants as $plant)
                        <tr>
                            <td>
                                @if($plant->type === 'AMP')
                                    <span class="badge bg-info">AMP</span>
                                @else
                                    <span class="badge bg-warning text-dark">CBP</span>
                                @endif
                            </td>
                            <td>{{ $plant->address }}</td>
                            <td>
                                <form action="{{ route('admin.companies.plants.destroy', [$company, $plant]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus lokasi plant ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="small text-dim">Tidak ada lokasi plant yang terdaftar.</p>
    @endif
    <hr class="border-secondary my-4">
    <h6 class="mb-2">Pengguna Terkait</h6>
    <ul class="small mb-0">
        @forelse($company->users as $u)
            <li>{{ $u->name }} ({{ $u->email }}) @if(!$u->approved_at)<span class="badge bg-warning text-dark">Pending</span>@endif</li>
        @empty
            <li class="text-dim">Tidak ada</li>
        @endforelse
    </ul>
</div>
@endsection
