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

        // Seizure metadata (FR2)
        'device_type',
        'make_model',
        'serial_number',
        'description',
        'source',
        'seized_at',
        'seizure_location',
        'seizing_officer_id',
        'witness_name',

        // Acquisition details (CS 422 worksheet 8A)
        'source_state',
        'write_blocker',
        'acquisition_method',
        'acquisition_scope',
        'image_format',
        'acquisition_tool',
        'acquisition_tool_version',
        'acquired_by',
        'acquisition_started_at',
        'acquisition_completed_at',
        'acquisition_timezone',
        'acquisition_notes',

        // Integrity (FR3, worksheet 8B)
        'original_filename',
        'file_path',
        'file_size',
        'sha256_hash',
        'hash_algorithm',
        'hashed_at',

        // Physical exhibits
        'storage_locker',

        'status',
        'registered_by',
        'preservation_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'seized_at'                => 'datetime',
            'hashed_at'                => 'datetime',
            'acquisition_started_at'   => 'datetime',
            'acquisition_completed_at' => 'datetime',
            'preservation_expires_at'  => 'date',
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

    /**
     * The examiner who performed the acquisition. Not always the same
     * person as the officer who seized the device.
     */
    public function acquiredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acquired_by');
    }

    public function isDigital(): bool
    {
        return $this->kind === 'digital';
    }

    /**
     * How long the acquisition took, for the acquisition log.
     */
    public function acquisitionDuration(): ?string
    {
        if (! $this->acquisition_started_at || ! $this->acquisition_completed_at) {
            return null;
        }

        return $this->acquisition_started_at
            ->diff($this->acquisition_completed_at)
            ->format('%hh %im');
    }
}
