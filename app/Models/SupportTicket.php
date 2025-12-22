<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'subject',
        'description',
        'category',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_PENDING_USER_ACTION = 'pending_user_action';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    // Priority constants
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // Category constants
    public const CATEGORY_BUSINESS_DATA = 'business_data';
    public const CATEGORY_EMAIL_CHANGE = 'email_change';
    public const CATEGORY_ACCOUNT_ACCESS = 'account_access';
    public const CATEGORY_TECHNICAL_ISSUE = 'technical_issue';
    public const CATEGORY_OTHER = 'other';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public static function generateNumber(): string
    {
        return 'TKT-' . date('Ymd') . '-' . str_pad((string) (static::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopePendingUserAction($query)
    {
        return $query->where('status', self::STATUS_PENDING_USER_ACTION);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_OPEN => 'Terbuka',
            self::STATUS_IN_PROGRESS => 'Dalam Proses',
            self::STATUS_PENDING_USER_ACTION => 'Menunggu Tindakan User',
            self::STATUS_RESOLVED => 'Terselesaikan',
            self::STATUS_CLOSED => 'Ditutup',
        ];
    }

    public static function getPriorityLabels(): array
    {
        return [
            self::PRIORITY_LOW => 'Rendah',
            self::PRIORITY_MEDIUM => 'Sedang',
            self::PRIORITY_HIGH => 'Tinggi',
            self::PRIORITY_URGENT => 'Mendesak',
        ];
    }

    public static function getCategoryLabels(): array
    {
        return [
            self::CATEGORY_BUSINESS_DATA => 'Perubahan Data BU',
            self::CATEGORY_EMAIL_CHANGE => 'Perubahan Email',
            self::CATEGORY_ACCOUNT_ACCESS => 'Akses Akun',
            self::CATEGORY_TECHNICAL_ISSUE => 'Masalah Teknis',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public function getStatusColorClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'badge-info',
            self::STATUS_IN_PROGRESS => 'badge-primary',
            self::STATUS_PENDING_USER_ACTION => 'badge-warning',
            self::STATUS_RESOLVED => 'badge-success',
            self::STATUS_CLOSED => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    public function getPriorityColorClass(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'badge-success',
            self::PRIORITY_MEDIUM => 'badge-info',
            self::PRIORITY_HIGH => 'badge-warning',
            self::PRIORITY_URGENT => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}
