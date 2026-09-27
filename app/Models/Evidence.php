<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    use HasFactory;

    /**
     * Laravel would guess 'evidences'. The table is 'evidence'.
     */
    protected $table = 'evidence';

    protected $fillable = [
        'investigation_case_id',
        'evidence_number',
        'kind',
        'device_type',
        'make_model',
        'serial_number',
        'description',
        'source',
        'seized_at',
        'seizure_location',
        'seizing_officer_id',
        'witness_name',
        'original_filename',
        'file_path',
        'file_size',
        'sha256_hash',
        'hashed_at',
        'storage_locker',
        'status',
        'registered_by',
        'preservation_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'seized_at' => 'datetime',
            'hashed_at' => 'datetime',
            'preservation_expires_at' => 'date',
        ];
    }

    public function investigationCase(): BelongsTo
    {
        return $this->belongsTo(InvestigationCase::class);
    }

    public function seizingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seizing_officer_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function isDigital(): bool
    {
        return $this->kind === 'digital';
    }
}
