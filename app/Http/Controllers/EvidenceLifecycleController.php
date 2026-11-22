<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvidenceLifecycleController extends Controller
{
    public function index()
    {
        $evidences = Evidence::with(['investigationCase', 'currentHolder', 'disposalApprover'])
            ->latest()
            ->get();

        return view('retention.index', compact('evidences'));
    }

    public function setRetention(Request $request, Evidence $evidence)
    {
        $request->validate([
            'retention_until' => 'required|date',
        ]);

        $evidence->update([
            'retention_until' => $request->retention_until,
        ]);

        return back()->with('success', "Retention deadline updated for {$evidence->evidence_number}.");
    }

    public function archive(Evidence $evidence)
    {
        if ($evidence->lifecycle_status === 'disposed') {
            return back()->with('error', 'Disposed evidence cannot be archived.');
        }

        $evidence->update([
            'lifecycle_status' => 'archived',
            'archived_at'      => now(),
        ]);

        return back()->with('success', "{$evidence->evidence_number} has been archived.");
    }

    public function dispose(Request $request, Evidence $evidence)
    {
        $request->validate([
            'disposal_reason' => 'required|string|max:500',
        ]);

        $evidence->update([
            'lifecycle_status'     => 'disposed',
            'disposed_at'          => now(),
            'disposal_approved_by' => Auth::id() ?? 1,
            'disposal_reason'      => $request->disposal_reason,
        ]);

        return back()->with('success', "Disposal approved and logged for {$evidence->evidence_number}.");
    }
}