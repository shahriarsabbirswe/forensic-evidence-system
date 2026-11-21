<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestigationCaseRequest;
use App\Http\Requests\UpdateInvestigationCaseRequest;
use App\Models\InvestigationCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestigationCaseController extends Controller
{
    /**
     * FR1: list cases, with a simple search and status filter.
     */
    public function index(Request $request)
    {
        $cases = InvestigationCase::query()
            ->with('openedBy')
            ->withCount('evidence')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->input('q');
                $query->where(function ($inner) use ($term) {
                    $inner->where('case_number', 'like', "%{$term}%")
                          ->orWhere('title', 'like', "%{$term}%")
                          ->orWhere('crime_type', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('opened_at')
            ->paginate(15)
            ->withQueryString();

        return view('cases.index', compact('cases'));
    }

    public function create()
    {
        return view('cases.create', [
            'case' => new InvestigationCase(),
        ]);
    }

    public function store(StoreInvestigationCaseRequest $request)
    {
        $case = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $data['case_number']  = $this->nextCaseNumber();
            $data['opened_by']    = $request->user()->id;

            // Cyber Security Act 2026, Section 32: 90 days from the start.
            $data['investigation_deadline'] = Carbon::parse($data['opened_at'])
                ->addDays(90)
                ->toDateString();
            $data['extension_stage'] = 0;

            return InvestigationCase::create($data);
        });

        return redirect()
            ->route('cases.show', $case)
            ->with('status', "Case {$case->case_number} created.");
    }

    public function show(InvestigationCase $case)
    {
        $case->load(['openedBy', 'assignedUsers', 'evidence']);

        // People not yet on this case.
        $assignedIds = $case->assignedUsers->pluck('id');
        $availableUsers = User::whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->get();

        return view('cases.show', compact('case', 'availableUsers'));
    }

    public function edit(InvestigationCase $case)
    {
        return view('cases.edit', compact('case'));
    }

    public function update(UpdateInvestigationCaseRequest $request, InvestigationCase $case)
    {
        $case->update($request->validated());

        return redirect()
            ->route('cases.show', $case)
            ->with('status', 'Case updated.');
    }

    /**
     * FR1: assign a person to the case.
     */
    public function assign(Request $request, InvestigationCase $case)
    {
        $data = $request->validate([
            'user_id'      => ['required', 'exists:users,id'],
            'role_in_case' => ['required', 'string', 'max:100'],
        ]);

        $alreadyOnCase = $case->assignedUsers()
            ->where('users.id', $data['user_id'])
            ->exists();

        if ($alreadyOnCase) {
            return back()->withErrors([
                'user_id' => 'That person is already assigned to this case.',
            ]);
        }

        $case->assignedUsers()->attach($data['user_id'], [
            'role_in_case' => $data['role_in_case'],
            'assigned_at'  => now(),
            'assigned_by'  => $request->user()->id,
        ]);

        return back()->with('status', 'Person assigned to the case.');
    }

    public function unassign(InvestigationCase $case, User $user)
    {
        $case->assignedUsers()->detach($user->id);

        return back()->with('status', 'Assignment removed.');
    }

    /**
     * Next case number for the current year, e.g. DFC-2026-0001.
     * Runs inside the store transaction so two users cannot take
     * the same number at the same moment.
     */
    private function nextCaseNumber(): string
    {
        $year = now()->year;

        $last = InvestigationCase::where('case_number', 'like', "DFC-{$year}-%")
            ->lockForUpdate()
            ->orderByDesc('case_number')
            ->value('case_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return sprintf('DFC-%d-%04d', $year, $next);
    }
}
