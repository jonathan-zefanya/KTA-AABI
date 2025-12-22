@extends('layouts.user')

@section('title','Buat Tiket Dukungan')

@section('content')
<!-- Flash Messages -->
<div class="flash-area">
    @if ($errors->any())
        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Page Header -->
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('support-tickets.index') }}" style="color: var(--ui-primary); font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <i class="bi bi-chevron-left"></i>Kembali ke Tiket
                </a>
                <h3 style="margin: 0; font-weight: 700; font-size: 1.5rem;">Buat Tiket Dukungan Baru</h3>
                <p style="margin: 0.5rem 0 0 0; color: #8b92a3; font-size: 0.85rem;">Jelaskan masalah atau permintaan Anda</p>
            </div>

            <!-- Form -->
            <form action="{{ route('support-tickets.store') }}" method="POST" class="surface" style="padding: 2rem; max-width: 700px;">
                @csrf

                <div class="mb-4">
                    <label for="subject" class="form-label" style="font-weight: 500; font-size: 0.85rem; margin-bottom: 0.5rem;">
                        <i class="bi bi-pencil-fill me-1"></i>Judul / Subjek <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" 
                           id="subject" 
                           name="subject"
                           class="form-control @error('subject') is-invalid @enderror"
                           style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem;"
                           placeholder="Contoh: Perubahan Data Perusahaan"
                           value="{{ old('subject') }}"
                           required>
                    @error('subject')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="category" class="form-label" style="font-weight: 500; font-size: 0.85rem; margin-bottom: 0.5rem;">
                            <i class="bi bi-bookmark-fill me-1"></i>Kategori <span style="color: #dc2626;">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                class="form-select @error('category') is-invalid @enderror"
                                style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem;"
                                required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="business_data" @selected(old('category') === 'business_data')>Perubahan Data BU</option>
                            <option value="email_change" @selected(old('category') === 'email_change')>Perubahan Email</option>
                            <option value="account_access" @selected(old('category') === 'account_access')>Akses Akun</option>
                            <option value="technical_issue" @selected(old('category') === 'technical_issue')>Masalah Teknis</option>
                            <option value="other" @selected(old('category') === 'other')>Lainnya</option>
                        </select>
                        @error('category')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="priority" class="form-label" style="font-weight: 500; font-size: 0.85rem; margin-bottom: 0.5rem;">
                            <i class="bi bi-exclamation-lg me-1"></i>Prioritas <span style="color: #dc2626;">*</span>
                        </label>
                        <select id="priority"
                                name="priority"
                                class="form-select @error('priority') is-invalid @enderror"
                                style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem;"
                                required>
                            <option value="">-- Pilih Prioritas --</option>
                            <option value="low" @selected(old('priority') === 'low')>Rendah</option>
                            <option value="medium" @selected(old('priority') === 'medium' || !old('priority'))>Sedang</option>
                            <option value="high" @selected(old('priority') === 'high')>Tinggi</option>
                            <option value="urgent" @selected(old('priority') === 'urgent')>Mendesak</option>
                        </select>
                        @error('priority')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label" style="font-weight: 500; font-size: 0.85rem; margin-bottom: 0.5rem;">
                        <i class="bi bi-file-text-fill me-1"></i>Deskripsi Lengkap <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              style="border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem; min-height: 200px; resize: vertical;"
                              placeholder="Jelaskan masalah atau permintaan Anda secara detail..."
                              required>{{ old('description') }}</textarea>
                    <small class="text-muted d-block mt-1">Minimal 10 karakter, maksimal 5000 karakter</small>
                    @error('description')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div style="border-top: 1px solid var(--ui-border-soft); padding-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="{{ route('support-tickets.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Buat Tiket
                    </button>
                </div>
            </form>

            <!-- Tips Section -->
            <div class="surface" style="padding: 1.5rem; margin-top: 2rem; background-color: #eff6ff;">
                <h6 style="margin: 0 0 1rem 0; color: #1d4ed8; font-weight: 600; font-size: 0.9rem;">
                    <i class="bi bi-lightbulb-fill me-1"></i>Tips untuk Tiket yang Lebih Baik
                </h6>
                <ul style="margin: 0; padding-left: 1.5rem; color: #1d4ed8; font-size: 0.85rem; line-height: 1.6;">
                    <li>Berikan judul yang jelas dan spesifik</li>
                    <li>Jelaskan masalah Anda secara detail dan ringkas</li>
                    <li>Sebutkan langkah-langkah yang telah Anda coba</li>
                    <li>Sertakan informasi relevan jika ada (contoh: nomor invoice, tanggal, dll)</li>
                    <li>Pilih prioritas sesuai dengan urgensi masalah Anda</li>
                </ul>
            </div>
        </div>
@endsection
