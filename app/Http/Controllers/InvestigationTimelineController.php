<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;

class InvestigationTimelineController extends Controller
{
    public function show(Evidence $evidence)
    {
        $events = collect();

        // 1. Seizure Event (FR2)
        if ($evidence->seized_at) {
            $events->push([
                'timestamp' => $evidence->seized_at,
                'category'  => 'Seizure',
                'title'     => 'Evidence Seized at Scene',
                'actor'     => $evidence->seizingOfficer->name ?? 'Seizing Officer',
                'details'   => 'Location: ' . ($evidence->seizure_location ?? 'N/A') . ' | Source: ' . ($evidence->source ?? 'N/A'),
            ]);
        }

        // 2. System Registration Event
        $events->push([
            'timestamp' => $evidence->created_at,
            'category'  => 'Intake',
            'title'     => 'Registered in Forensic System (' . $evidence->evidence_number . ')',
            'actor'     => $evidence->registeredBy->name ?? 'Intake Officer',
            'details'   => 'Type: ' . ucfirst($evidence->kind) . ' | Description: ' . ($evidence->description ?? 'Logged at intake'),
        ]);

        // 3. Forensic Acquisition Event
        if ($evidence->acquisition_started_at) {
            $events->push([
                'timestamp' => $evidence->acquisition_started_at,
                'category'  => 'Acquisition',
                'title'     => 'Forensic Acquisition Started (' . ($evidence->acquisition_tool ?? 'Tool') . ')',
                'actor'     => $evidence->acquiredBy->name ?? 'Forensic Examiner',
                'details'   => 'Method: ' . ($evidence->acquisition_method ?? 'N/A') . ' | Duration: ' . ($evidence->acquisitionDuration() ?? 'In progress'),
            ]);
        }

        // 4. Integrity Hash Verification (FR3)
        if ($evidence->hashed_at) {
            $events->push([
                'timestamp' => $evidence->hashed_at,
                'category'  => 'Integrity',
                'title'     => 'Cryptographic Hash Generated (' . ($evidence->hash_algorithm ?? 'SHA-256') . ')',
                'actor'     => $evidence->acquiredBy->name ?? 'System',
                'details'   => 'Hash: ' . substr($evidence->sha256_hash ?? '', 0, 24) . '...',
            ]);
        }

        // 5. Chain of Custody Ledger Entries (Module 2)
        foreach ($evidence->custodyEntries as $entry) {
            $events->push([
                'timestamp' => $entry->occurred_at ?? $entry->created_at,
                'category'  => 'Custody',
                'title'     => 'Custody Log: ' . ucfirst($entry->action ?? 'Transfer / Check'),
                'actor'     => $entry->actor->name ?? ($entry->fromUser->name ?? 'Custody Officer'),
                'details'   => $entry->notes ?? ('Seal Status: ' . ($entry->seal_condition ?? 'Verified')),
            ]);
        }

        // 6. Archival Event (FR12)
        if ($evidence->archived_at) {
            $events->push([
                'timestamp' => $evidence->archived_at,
                'category'  => 'Lifecycle',
                'title'     => 'Evidence Moved to Archive',
                'actor'     => 'Authorized Officer',
                'details'   => 'Lifecycle status updated to Archived for long-term retention.',
            ]);
        }

        // 7. Disposal Event (FR12)
        if ($evidence->disposed_at) {
            $events->push([
                'timestamp' => $evidence->disposed_at,
                'category'  => 'Disposal',
                'title'     => 'Evidence Disposed (Approved)',
                'actor'     => $evidence->disposalApprover->name ?? 'Authorized Admin',
                'details'   => 'Reason: ' . ($evidence->disposal_reason ?? 'Retention period expired'),
            ]);
        }

        // Chronological sorting (oldest to newest)
        $timeline = $events->sortBy('timestamp')->values();

        return view('timeline.show', compact('evidence', 'timeline'));
    }
}