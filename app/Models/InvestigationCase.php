<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestigationCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number',
        'title',
        'crime_type',
        'jurisdiction',
        'description',
        'status',
        'opened_by',
        'opened_at',
        'investigation_deadline',
        'extension_stage',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'investigation_deadline' => 'date',
        ];
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'case_assignments')
            ->withPivot(['role_in_case', 'assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    /**
     * Days left against the Section 32 deadline.
     * Negative means the deadline has passed.
     */
    public function daysRemaining(): ?int
    {
        if (! $this->investigation_deadline) {
            return null;
        }

        return (int) now()->startOfDay()
            ->diffInDays($this->investigation_deadline, false);
    }

    public function isOverdue(): bool
    {
        $days = $this->daysRemaining();

        return $days !== null && $days < 0;
    }
}
