@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="h4 fw-bold mb-1">Lokasi Pabrik</h2>
            <p class="text-secondary small">Kelola Asphalt Mixing Plant (AMP) dan Concrete Batching Plant (CBP)</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('company.plants.create') }}" class="btn btn-primary btn-sm">+ Tambah Lokasi</a>
        </div>
    </div>

    @if($plants->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:100px">Jenis</th>
                        <th>Alamat</th>
                        <th style="width:120px">Ditambah</th>
                        <th style="width:90px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plants as $plant)
                        <tr>
                            <td>
                                <span class="badge {{ $plant->type === 'AMP' ? 'bg-info' : 'bg-success' }}">
                                    {{ $plant->type === 'AMP' ? 'AMP' : 'CBP' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-dark">{{ $plant->address }}</small>
                            </td>
                            <td>
                                <small class="text-secondary">{{ $plant->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('company.plants.edit', $plant) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('company.plants.destroy', $plant) }}" class="d-inline" onsubmit="return confirm('Yakin dihapus?')">
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

        <nav aria-label="Page navigation">
            {{ $plants->links() }}
        </nav>
    @else
        <div class="alert alert-info py-4 text-center">
            <p class="mb-2">Belum ada lokasi pabrik yang terdaftar.</p>
            <a href="{{ route('company.plants.create') }}" class="btn btn-sm btn-primary">Tambah Lokasi Pertama</a>
        </div>
    @endif
</div>
@endsection
