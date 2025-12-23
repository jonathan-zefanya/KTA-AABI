@extends('admin.layout')

@section('title','Tiket Dukungan')
@section('page_title','Kelola Tiket Dukungan')

@section('content')
<style>
.filter-section {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.ticket-row {
    display: grid;
    grid-template-columns: auto 1fr auto auto auto auto;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    background: var(--adm-bg-alt);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
}
.ticket-row:hover {
    border-color: var(--adm-accent);
    background: var(--adm-surface);
}
.ticket-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.ticket-number {
    font-size: 0.7rem;
    color: var(--adm-text-dim);
    font-weight: 600;
    letter-spacing: 0.5px;
}
.ticket-subject {
    color: var(--adm-text);
    font-weight: 500;
    font-size: 0.95rem;
}
.ticket-user {
    color: var(--adm-text-dim);
    font-size: 0.8rem;
}
.ticket-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}
@media (max-width: 1200px) {
    .ticket-row {
        grid-template-columns: 1fr auto;
    }
    .ticket-meta {
        grid-column: 1 / -1;
        margin-top: 0.5rem;
    }
}
</style>

<!-- Filter Section -->
<div class="filter-section">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-lg-3 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-search me-1"></i>Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="form-control form-control-sm bg-dark border-secondary text-light" 
                   placeholder="Nomor tiket / Subjek / Pengguna">
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-funnel me-1"></i>Status</label>
            <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-exclamation-diamond me-1"></i>Prioritas</label>
            <select name="priority" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach ($priorities as $key => $label)
                    <option value="{{ $key }}" @selected(request('priority') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-bookmark me-1"></i>Kategori</label>
            <select name="category" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small">Dari Tanggal</label>
            <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control form-control-sm">
        </div>

        <div class="col-md-3">
            <label class="form-label small">Sampai Tanggal</label>
            <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control form-control-sm">
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-person-fill me-1"></i>Ditugaskan</label>
            <select name="assigned_to" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach ($admins as $admin)
                    <option value="{{ $admin->id }}" @selected(request('assigned_to') == $admin->id)>{{ $admin->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-1 col-md-6">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Terapkan</button>
        </div>
        @if (request()->anyFilled(['search', 'status', 'priority', 'category', 'assigned_to']))
            <div class="col-12">
                <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Hapus Filter
                </a>
            </div>
        @endif
    </form>
</div>

<!-- Tickets List -->
@if ($tickets->count() > 0)
    <div class="mb-4">
        @foreach ($tickets as $ticket)
            <div class="ticket-row">
                <div style="min-width: 100px;">
                    <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="btn btn-sm btn-ghost">
                        <i class="bi bi-arrow-right me-1"></i>Lihat
                    </a>
                </div>
                <div class="ticket-info">
                    <div class="ticket-number">{{ $ticket->ticket_number }}</div>
                    <div class="ticket-subject">{{ $ticket->subject }}</div>
                    <div class="ticket-user">
                        <i class="bi bi-person-circle me-1"></i>{{ $ticket->user->name }} ({{ $ticket->user->email }})
                    </div>
                </div>
                <div class="ticket-meta" style="gap: 0.75rem;">
                    <span class="badge {{ $ticket->getStatusColorClass() }}">{{ $statuses[$ticket->status] }}</span>
                    <span class="badge {{ $ticket->getPriorityColorClass() }}">{{ $priorities[$ticket->priority] }}</span>
                </div>
                <div style="text-align: right; font-size: 0.75rem; color: var(--adm-text-dim);">
                    <div>{{ $ticket->created_at->diffForHumans() }}</div>
                    <div>{{ $categories[$ticket->category] ?? $ticket->category }}</div>
                </div>
                @if ($ticket->assignedAdmin)
                    <div style="text-align: right; font-size: 0.75rem;">
                        <span class="badge badge-info">{{ $ticket->assignedAdmin->name }}</span>
                    </div>
                @else
                    <div style="text-align: right; font-size: 0.75rem; color: var(--adm-text-dim);">
                        <span class="badge badge-secondary">Belum Ditugaskan</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    {{ $tickets->links('pagination::bootstrap-5') }}
@else
    <div class="adm-card text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem; color: var(--adm-text-dim); opacity: 0.5;"></i>
        <p class="text-dim mt-3">Tidak ada tiket dukungan ditemukan</p>
    </div>
@endif

@endsection
