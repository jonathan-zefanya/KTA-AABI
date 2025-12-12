@extends('layouts.user')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold mb-4">Tambah Lokasi Pabrik</h3>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('company.plants.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Jenis Lokasi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Pilih jenis lokasi</option>
                                <option value="AMP" @selected(old('type') === 'AMP')>Asphalt Mixing Plant (AMP)</option>
                                <option value="CBP" @selected(old('type') === 'CBP')>Concrete Batching Plant (CBP)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-medium">Alamat <span class="text-danger">*</span></label>
                            <textarea name="address" rows="4" class="form-control @error('address') is-invalid @enderror" placeholder="Masukkan alamat lengkap lokasi pabrik" required>{{ old('address') }}</textarea>
                            <small class="form-text text-secondary">Maksimal 500 karakter</small>
                            @error('address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('company.plants.index') }}" class="btn btn-outline-secondary flex-grow-1">Kembali</a>
                            <button type="submit" class="btn btn-primary flex-grow-1">Simpan Lokasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
