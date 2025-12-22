@extends('layouts.user')

@section('title','Tiket #' . $supportTicket->ticket_number)

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
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('support-tickets.index') }}" style="color: var(--ui-primary); font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <i class="bi bi-chevron-left"></i>Kembali ke Tiket
                </a>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-weight: 700; font-size: 1.3rem;">{{ $supportTicket->subject }}</h3>
                        <p style="margin: 0.5rem 0 0 0; color: #8b92a3; font-size: 0.85rem; font-family: monospace;">Tiket #{{ $supportTicket->ticket_number }}</p>
                    </div>
                    <span class="status-badge @switch($supportTicket->status)
                        @case('open') info @break
                        @case('in_progress') warning @break
                        @case('pending_user_action') warning @break
                        @case('resolved') success @break
                        @case('closed') neutral @break
                    @endswitch" style="font-size: 0.9rem;">
                        {{ \App\Models\SupportTicket::getStatusLabels()[$supportTicket->status] }}
                    </span>
                </div>
            </div>

            <div class="row g-3">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Details Card -->
                    <div class="surface" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h5 style="margin: 0 0 1rem 0; font-weight: 600; font-size: 0.95rem;">
                            <i class="bi bi-info-circle-fill me-1"></i>Informasi Tiket
                        </h5>

                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--ui-border-soft);">
                            <div style="color: #8b92a3; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
                            <div>
                                <span class="status-badge @switch($supportTicket->status)
                                    @case('open') info @break
                                    @case('in_progress') warning @break
                                    @case('pending_user_action') warning @break
                                    @case('resolved') success @break
                                    @case('closed') neutral @break
                                @endswitch">
                                    {{ \App\Models\SupportTicket::getStatusLabels()[$supportTicket->status] }}
                                </span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--ui-border-soft);">
                            <div style="color: #8b92a3; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Kategori</div>
                            <div>
                                <span class="status-badge info">
                                    {{ \App\Models\SupportTicket::getCategoryLabels()[$supportTicket->category] ?? $supportTicket->category }}
                                </span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--ui-border-soft);">
                            <div style="color: #8b92a3; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Prioritas</div>
                            <div>
                                <span class="status-badge @switch($supportTicket->priority)
                                    @case('low') success @break
                                    @case('medium') info @break
                                    @case('high') warning @break
                                    @case('urgent') danger @break
                                @endswitch">
                                    {{ \App\Models\SupportTicket::getPriorityLabels()[$supportTicket->priority] }}
                                </span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 1rem; margin-bottom: 0;">
                            <div style="color: #8b92a3; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Dibuat</div>
                            <div>
                                <div style="font-size: 0.9rem;">{{ $supportTicket->created_at->format('d M Y \p\u\k\u\l H:i') }}</div>
                                <small style="color: #8b92a3; font-size: 0.8rem;">{{ $supportTicket->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        @if ($supportTicket->resolved_at)
                            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--ui-border-soft);">
                                <div style="color: #8b92a3; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Diselesaikan</div>
                                <div style="font-size: 0.9rem;">{{ $supportTicket->resolved_at->format('d M Y H:i') }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Description Card -->
                    <div class="surface" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h5 style="margin: 0 0 1rem 0; font-weight: 600; font-size: 0.95rem;">
                            <i class="bi bi-file-text-fill me-1"></i>Deskripsi
                        </h5>
                        <div style="background: var(--ui-surface-alt); padding: 1rem; border-radius: 8px; color: #333; line-height: 1.6; white-space: pre-wrap; word-break: break-word; font-size: 0.9rem;">
                            {{ $supportTicket->description }}
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    @if ($supportTicket->notes)
                        <div class="surface" style="padding: 1.5rem; border-left: 3px solid var(--ui-primary);">
                            <h5 style="margin: 0 0 1rem 0; font-weight: 600; font-size: 0.95rem;">
                                <i class="bi bi-chat-left-text-fill me-1" style="color: var(--ui-primary);"></i>Catatan dari Tim Admin
                            </h5>
                            <div style="background: var(--ui-surface-alt); padding: 1rem; border-radius: 8px; color: #333; line-height: 1.6; white-space: pre-wrap; word-break: break-word; font-size: 0.9rem;">
                                {{ $supportTicket->notes }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Status Information -->
                    <div class="surface" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h6 style="margin: 0 0 1rem 0; font-weight: 600; font-size: 0.85rem; color: #8b92a3; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-info-circle me-1"></i>Status Saat Ini
                        </h6>

                        @if ($supportTicket->status === 'open')
                            <div style="background: #eff6ff; border-radius: 8px; padding: 1rem; border-left: 3px solid #1d4ed8;">
                                <p style="margin: 0; color: #1d4ed8; font-size: 0.85rem; font-weight: 500;">
                                    <i class="bi bi-clock-history me-1"></i>Tiket Anda telah diterima dan menunggu untuk ditinjau oleh tim admin.
                                </p>
                            </div>
                        @elseif ($supportTicket->status === 'in_progress')
                            <div style="background: #fff7ed; border-radius: 8px; padding: 1rem; border-left: 3px solid #f59e0b;">
                                <p style="margin: 0; color: #9a3412; font-size: 0.85rem; font-weight: 500;">
                                    <i class="bi bi-hourglass-split me-1"></i>Tim admin sedang mengerjakan tiket Anda.
                                </p>
                            </div>
                        @elseif ($supportTicket->status === 'pending_user_action')
                            <div style="background: #fff7ed; border-radius: 8px; padding: 1rem; border-left: 3px solid #f59e0b;">
                                <p style="margin: 0; color: #9a3412; font-size: 0.85rem; font-weight: 500;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Menunggu tindakan atau informasi tambahan dari Anda.
                                </p>
                            </div>
                        @elseif ($supportTicket->status === 'resolved')
                            <div style="background: #ecfdf5; border-radius: 8px; padding: 1rem; border-left: 3px solid #10b981;">
                                <p style="margin: 0; color: #065f46; font-size: 0.85rem; font-weight: 500;">
                                    <i class="bi bi-check-circle me-1"></i>Tiket Anda telah diselesaikan. Silakan verifikasi solusinya.
                                </p>
                            </div>
                        @elseif ($supportTicket->status === 'closed')
                            <div style="background: #f1f5f9; border-radius: 8px; padding: 1rem; border-left: 3px solid #475569;">
                                <p style="margin: 0; color: #334155; font-size: 0.85rem; font-weight: 500;">
                                    <i class="bi bi-lock me-1"></i>Tiket ini telah ditutup.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Assigned Admin -->
                    <div class="surface" style="padding: 1.5rem;">
                        <h6 style="margin: 0 0 1rem 0; font-weight: 600; font-size: 0.85rem; color: #8b92a3; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="bi bi-person-circle me-1"></i>Petugas
                        </h6>

                        @if ($supportTicket->assignedAdmin)
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: var(--ui-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                    {{ substr($supportTicket->assignedAdmin->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500; font-size: 0.9rem;">{{ $supportTicket->assignedAdmin->name }}</div>
                                    <small style="color: #8b92a3;">Admin</small>
                                </div>
                            </div>
                        @else
                            <p style="margin: 0; color: #8b92a3; font-size: 0.85rem;">
                                <i class="bi bi-info-circle me-1"></i>Belum ditugaskan ke admin
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
@endsection
