<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class CustodyEntry extends Model
{
    protected $table = 'custody_entries';

    protected $fillable = [
        'evidence_id',
        'evidence_transfer_id',
        'action',
        'from_user_id',
        'to_user_id',
        'recorded_by',
        'release_seal_condition',
        'receipt_seal_condition',
        'reason',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }

    /**
     * Append-only enforcement: block any attempt to modify or delete ledger rows.
     */
    protected static function ==(): void
    {
        static::updating(function () {
            throw new RuntimeException('Chain of custody ledger entries are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new RuntimeException('Chain of custody ledger entries are immutable and cannot be deleted.');
        });
    }

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(EvidenceTransfer::class, 'evidence_transfer_id');
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}