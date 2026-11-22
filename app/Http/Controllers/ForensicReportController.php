<?php

namespace App\Http\Controllers;

use App\Models\InvestigationCase;
use App\Services\ForensicReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForensicReportController extends Controller
{
    public function __construct(
        private readonly ForensicReportService $reportService
    ) {}

    /**
     * Stream the court-ready forensic report PDF inline in browser.
     */
    public function preview(Request $request, InvestigationCase $case)
    {
        $user = $request->user();
        abort_unless($user && $user->can('case.view'), 403);

        $data = $this->reportService->buildCaseReportData($case, $user);

        $pdf = Pdf::loadView('reports.case-forensic-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        $filename = 'Forensic-Report-' . Str::slug($case->case_number ?? 'case-' . $case->id) . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Force download the court-ready forensic report PDF.
     */
    public function download(Request $request, InvestigationCase $case)
    {
        $user = $request->user();
        abort_unless($user && $user->can('case.view'), 403);

        $data = $this->reportService->buildCaseReportData($case, $user);

        $pdf = Pdf::loadView('reports.case-forensic-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        $filename = 'Court-Forensic-Report-' . Str::slug($case->case_number ?? 'case-' . $case->id) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}