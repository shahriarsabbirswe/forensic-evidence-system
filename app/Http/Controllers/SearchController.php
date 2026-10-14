<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\InvestigationCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Check if the user submitted any search filters
        if ($request->anyFilled(['q', 'case_id', 'status', 'case_status', 'from', 'to', 'officer_id', 'hash'])) {
            $query = Evidence::query();

            // 1. Free text filter: evidence fields, plus case number and title
            $query->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->input('q')) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('evidence_number', 'like', $term)
                        ->orWhere('device_type', 'like', $term)
                        ->orWhere('make_model', 'like', $term)
                        ->orWhere('serial_number', 'like', $term)
                        ->orWhereIn(
                            'investigation_case_id',
                            InvestigationCase::select('id')
                                ->where('case_number', 'like', $term)
                                ->orWhere('title', 'like', $term)
                        );
                });
            });

            // 2. Specific case filter
            $query->when($request->filled('case_id'), function ($q) use ($request) {
                $q->where('investigation_case_id', $request->input('case_id'));
            });

            // 3a. Evidence status filter
            $query->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            });

            // 3b. Case status filter
            $query->when($request->filled('case_status'), function ($q) use ($request) {
                $q->whereIn(
                    'investigation_case_id',
                    InvestigationCase::select('id')->where('status', $request->input('case_status'))
                );
            });

            // 4. Seizure date range filter
            $query->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('seized_at', '>=', $request->input('from'));
            });

            $query->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('seized_at', '<=', $request->input('to'));
            });

            // 5. Officer filter: seizing officer OR assigned analyst
            $query->when($request->filled('officer_id'), function ($q) use ($request) {
                $userId = $request->input('officer_id');
                $q->where(function ($sub) use ($userId) {
                    $sub->where('seizing_officer_id', $userId)
                        ->orWhereIn(
                            'investigation_case_id',
                            DB::table('case_assignments')
                                ->select('investigation_case_id')
                                ->where('user_id', $userId)
                        );
                });
            });

            // 6. SHA-256 hash filter
            $query->when($request->filled('hash'), function ($q) use ($request) {
                $hash = strtolower(trim($request->input('hash')));
                $q->where('sha256_hash', 'like', $hash . '%');
            });

            $results = $query->orderByDesc('seized_at')->paginate(10)->withQueryString();
        } else {
            // Return an empty result set when no search filter is active
            $results = Evidence::whereRaw('1 = 0')->paginate(10);
        }

        return view('search.index', [
            'results'      => $results,
            'cases'        => InvestigationCase::orderBy('case_number')->get(['id', 'case_number', 'title']),
            'statuses'     => Evidence::query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status'),
            'caseStatuses' => InvestigationCase::query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status'),
            'officers'     => User::orderBy('name')->get(['id', 'name']),
            'caseNumbers'  => InvestigationCase::pluck('case_number', 'id'),
            'userNames'    => User::pluck('name', 'id'),
        ]);
    }
}