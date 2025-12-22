@extends('admin.layout')

@section('title','Tiket #' . $supportTicket->ticket_number)
@section('page_title','Detail Tiket Dukungan')

@section('content')
<style>
.detail-card {
    background: var(--adm-bg-alt);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.detail-row {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--adm-border);
}
.detail-row:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}
.detail-label {
    font-weight: 600;
    color: var(--adm-text-dim);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.detail-value {
    color: var(--adm-text);
    word-break: break-word;
}
.description-box {
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: 6px;
    padding: 1rem;
    color: var(--adm-text);
    line-height: 1.6;
    white-space: pre-wrap;
    word-break: break-word;
}
.notes-box {
    background: var(--adm-surface);
    border-left: 3px solid var(--adm-accent);
    border-radius: 4px;
    padding: 1rem;
    margin-top: 1rem;
}
.action-button {
    display: inline-block;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}
</style>

<div class="row">
    <div class="col-lg-8">
        <!-- Ticket Details -->
        <div class="detail-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--adm-border);">
                <div>
                    <h6 style="margin: 0; color: var(--adm-text-dim); font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">Nomor Tiket</h6>
                    <h4 style="margin: 0.25rem 0 0 0; color: var(--adm-text); font-family: monospace;">{{ $supportTicket->ticket_number }}</h4>
                </div>
                <div style="text-align: right;">
                    <span class="badge {{ $supportTicket->getStatusColorClass() }} p-2" style="font-size: 0.9rem;">
                        {{ $statuses[$supportTicket->status] }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Subjek</div>
                <div class="detail-value" style="font-weight: 500; font-size: 1rem;">{{ $supportTicket->subject }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Pengguna</div>
                <div class="detail-value">
                    <a href="{{ route('admin.users.show', $supportTicket->user) }}" style="color: var(--adm-accent);">
                        {{ $supportTicket->user->name }}
                    </a>
                    <br><small class="text-dim">{{ $supportTicket->user->email }}</small>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Kategori</div>
                <div class="detail-value">
                    <span class="badge badge-info">{{ $categories[$supportTicket->category] ?? $supportTicket->category }}</span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Prioritas</div>
                <div class="detail-value">
                    <span class="badge {{ $supportTicket->getPriorityColorClass() }} p-2">
                        {{ $priorities[$supportTicket->priority] }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Dibuat</div>
                <div class="detail-value">
                    {{ $supportTicket->created_at->format('d M Y H:i') }}
                    <small class="text-dim">({{ $supportTicket->created_at->diffForHumans() }})</small>
                </div>
            </div>

            @if ($supportTicket->resolved_at)
                <div class="detail-row">
                    <div class="detail-label">Diselesaikan</div>
                    <div class="detail-value">
                        {{ $supportTicket->resolved_at->format('d M Y H:i') }}
                    </div>
                </div>
            @endif

            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--adm-border);">
                <h6 style="margin-bottom: 1rem; color: var(--adm-text-dim); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                    <i class="bi bi-file-text me-1"></i>Deskripsi
                </h6>
                <div class="description-box">{{ $supportTicket->description }}</div>
            </div>
        </div>

        @if ($supportTicket->notes)
            <div class="detail-card">
                <h6 style="margin-bottom: 1rem; color: var(--adm-text-dim); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                    <i class="bi bi-chat-left-text me-1"></i>Catatan Admin
                </h6>
                <div class="notes-box">{{ $supportTicket->notes }}</div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Action Panel -->
        <div class="detail-card">
            <h6 style="margin-bottom: 1rem; color: var(--adm-text-dim); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="bi bi-sliders me-1"></i>Kelola Tiket
            </h6>

            <form action="{{ route('admin.support-tickets.update', $supportTicket) }}" method="POST" class="mb-3">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small text-dim mb-2">Status</label>
                    <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light" required>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($supportTicket->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-dim mb-2">Prioritas</label>
                    <select name="priority" class="form-select form-select-sm bg-dark border-secondary text-light" required>
                        @foreach ($priorities as $key => $label)
                            <option value="{{ $key }}" @selected($supportTicket->priority === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-dim mb-2">Ditugaskan Kepada</label>
                    <select name="assigned_to" class="form-select form-select-sm bg-dark border-secondary text-light">
                        <option value="">Belum Ditugaskan</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}" @selected($supportTicket->assigned_to === $admin->id)>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-dim mb-2">Catatan</label>
                    <textarea name="notes" class="form-control form-control-sm bg-dark border-secondary text-light" rows="4" style="resize: vertical;">{{ $supportTicket->notes }}</textarea>
                </div>

                <button type="submit" class="btn btn-sm btn-primary w-100 action-button">
                    <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                </button>
            </form>

            @if ($supportTicket->status !== 'closed')
                <form action="{{ route('admin.support-tickets.close', $supportTicket) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Tutup tiket ini?')">
                        <i class="bi bi-lock me-1"></i>Tutup Tiket
                    </button>
                </form>
            @endif
        </div>

        <!-- Quick Info -->
        <div class="detail-card">
            <h6 style="margin-bottom: 1rem; color: var(--adm-text-dim); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="bi bi-info-circle me-1"></i>Informasi
            </h6>
            <div class="detail-row" style="grid-template-columns: 1fr;">
                <div>
                    <small class="text-dim">STATUS SAAT INI</small>
                    <div style="margin-top: 0.5rem;">
                        <span class="badge {{ $supportTicket->getStatusColorClass() }}">{{ $statuses[$supportTicket->status] }}</span>
                    </div>
                </div>
            </div>
            <div class="detail-row" style="grid-template-columns: 1fr;">
                <div>
                    <small class="text-dim">DITUGASKAN KEPADA</small>
                    <div style="margin-top: 0.5rem;">
                        @if ($supportTicket->assignedAdmin)
                            <span class="badge badge-info">{{ $supportTicket->assignedAdmin->name }}</span>
                        @else
                            <span class="badge badge-secondary">Belum Ditugaskan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
