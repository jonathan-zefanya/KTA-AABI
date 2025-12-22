@extends('layouts.user')

@section('title','Tiket Dukungan')

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
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif
</div>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h3 style="margin: 0; font-weight: 700; font-size: 1.5rem;">Tiket Dukungan</h3>
        <p style="margin: 0.5rem 0 0 0; color: #8b92a3; font-size: 0.85rem;">Kelola permintaan dukungan Anda</p>
    </div>
    <a href="{{ route('support-tickets.create') }}" class="btn btn-primary" style="gap: 0.5rem;">
        <i class="bi bi-plus-circle me-2"></i>Buat Tiket Baru
    </a>
</div>

<!-- Tickets List -->
@if ($tickets->count() > 0)
    <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
        @foreach ($tickets as $ticket)
            <a href="{{ route('support-tickets.show', $ticket) }}" style="text-decoration: none; color: inherit;">
                <div class="surface" style="padding: 1.25rem; display: grid; grid-template-columns: 1fr auto auto; gap: 1.5rem; align-items: center; cursor: pointer; transition: all var(--ui-transition);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <h5 style="margin: 0; font-weight: 600; font-size: 0.95rem; font-family: monospace;">{{ $ticket->ticket_number }}</h5>
                            <span class="status-badge @switch($ticket->status)
                                @case('open') info @break
                                @case('in_progress') warning @break
                                @case('pending_user_action') warning @break
                                @case('resolved') success @break
                                @case('closed') neutral @break
                            @endswitch">{{ \App\Models\SupportTicket::getStatusLabels()[$ticket->status] }}</span>
                        </div>
                        <p style="margin: 0.5rem 0 0 0; font-weight: 500; font-size: 1rem;">{{ $ticket->subject }}</p>
                        <p style="margin: 0.5rem 0 0 0; color: #8b92a3; font-size: 0.8rem;">
                            <i class="bi bi-bookmark me-1"></i>{{ \App\Models\SupportTicket::getCategoryLabels()[$ticket->category] ?? $ticket->category }}
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; color: #8b92a3; margin-bottom: 0.5rem;">
                            {{ $ticket->created_at->format('d M Y') }}
                        </div>
                        <span class="status-badge @switch($ticket->priority)
                            @case('low') success @break
                            @case('medium') info @break
                            @case('high') warning @break
                            @case('urgent') danger @break
                        @endswitch">{{ \App\Models\SupportTicket::getPriorityLabels()[$ticket->priority] }}</span>
                    </div>
                    <div style="text-align: right;">
                        @if ($ticket->assignedAdmin)
                            <div style="font-size: 0.75rem; color: #8b92a3; margin-bottom: 0.5rem;">Ditugaskan ke</div>
                            <div style="font-size: 0.85rem; font-weight: 500;">{{ $ticket->assignedAdmin->name }}</div>
                        @else
                            <div style="font-size: 0.8rem; color: #8b92a3;">Belum ditugaskan</div>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 2rem;">
        {{ $tickets->links('pagination::bootstrap-5') }}
    </div>
@else
    <div class="surface" style="padding: 3rem; text-align: center;">
        <i class="bi bi-inbox" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
        <h5 style="margin: 0; color: #8b92a3; font-weight: 500;">Tidak Ada Tiket</h5>
        <p style="margin: 0.75rem 0 1.5rem 0; color: #cbd5e1; font-size: 0.9rem;">Anda belum membuat tiket dukungan apapun</p>
        <a href="{{ route('support-tickets.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Buat Tiket Pertama
        </a>
    </div>
@endif
@endsection
