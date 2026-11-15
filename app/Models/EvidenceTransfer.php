<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceTransfer extends Model
{
    public const REQUESTED = 'requested';
    public const APPROVED = 'approved';
    public const RELEASED = 'released';
    public const COMPLETED = 'completed';
    public const REJECTED = 'rejected';
    public const CANCELLED = 'cancelled';

    /** A transfer in one of these states is still moving, so the item is "locked". */
    public const IN_FLIGHT = [self::REQUESTED, self::APPROVED, self::RELEASED];

    protected $fillable = [
        'evidence_id', 'from_user_id', 'to_user_id', 'requested_by', 'purpose', 'status',
        'approved_by', 'approved_at', 'approval_note',
        'released_at', 'release_seal_condition',
        'received_at', 'receipt_seal_condition',
        'closed_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isInFlight(): bool
    {
        return in_array($this->status, self::IN_FLIGHT, true);
    }

    /** Position on the four step progress bar (0 when rejected or cancelled). */
    public function step(): int
    {
        return [
            self::REQUESTED => 1,
            self::APPROVED => 2,
            self::RELEASED => 3,
            self::COMPLETED => 4,
        ][$this->status] ?? 0;
    }
}
