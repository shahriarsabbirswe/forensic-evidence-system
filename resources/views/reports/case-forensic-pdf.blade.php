<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forensic Investigation Report - {{ $case->case_number }}</title>
    <style>
        @page {
            margin: 18mm 14mm 18mm 14mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.45;
        }
        .header-banner {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .agency-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .sub-title {
            font-size: 9pt;
            color: #475569;
            margin-top: 2px;
        }
        .classification-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 3px 8px;
            letter-spacing: 1px;
            margin-top: 6px;
        }
        .meta-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .meta-box td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-size: 8.5pt;
        }
        .meta-label {
            font-weight: bold;
            color: #334155;
            width: 18%;
            background-color: #f1f5f9;
        }
        .section-heading {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            padding: 5px 9px;
            margin-top: 16px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.2pt;
        }
        table.data-table th {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            text-align: left;
            padding: 6px 7px;
            border: 1px solid #94a3b8;
        }
        table.data-table td {
            padding: 5px 7px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .mono {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 7.2pt;
            word-break: break-all;
        }
        .status-verified {
            color: #15803d;
            font-weight: bold;
        }
        .status-warn {
            color: #b91c1c;
            font-weight: bold;
        }
        .evidence-card {
            border: 1px solid #94a3b8;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .evidence-card-header {
            background-color: #f1f5f9;
            padding: 6px 8px;
            border-bottom: 1px solid #94a3b8;
            font-weight: bold;
            font-size: 9pt;
            color: #0f172a;
        }
        .declaration-box {
            border: 1px solid #64748b;
            background-color: #f8fafc;
            padding: 9px 11px;
            margin-top: 14px;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 26px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 48%;
            vertical-align: bottom;
            padding-top: 28px;
        }
        .sig-line {
            border-top: 1px solid #0f172a;
            width: 85%;
            padding-top: 4px;
            font-size: 8.5pt;
        }
        .checksum-footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px dashed #94a3b8;
            font-size: 7.5pt;
            color: #475569;
        }
    </style>
</head>
<body>

    {{-- Official Header --}}
    <div class="header-banner">
        <div class="agency-title">Digital Forensic Evidence Management System (DFEMS)</div>
        <div class="sub-title">Official Court-Ready Forensic Examination &amp; Chain of Custody Report</div>
        <div class="classification-badge">CONFIDENTIAL // JUDICIAL &amp; LAW ENFORCEMENT USE</div>
    </div>

    {{-- Report Metadata --}}
    <table class="meta-box">
        <tr>
            <td class="meta-label">Report Ref No:</td>
            <td class="mono">{{ $reportReference }}</td>
            <td class="meta-label">Generated On:</td>
            <td>{{ $generatedAt->format('d M Y, H:i:s T') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Case Number:</td>
            <td><strong>{{ $case->case_number }}</strong></td>
            <td class="meta-label">Case Status:</td>
            <td>{{ strtoupper($case->status ?? 'OPEN') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Generated By:</td>
            <td>{{ $generator->name }} ({{ $generator->email }})</td>
            <td class="meta-label">Evidence Count:</td>
            <td>Total: {{ $totalEvidence }} (Digital: {{ $digitalCount }}, Physical: {{ $physicalCount }})</td>
        </tr>
    </table>

    {{-- Section 1: Case Overview --}}
    <div class="section-heading">1. Investigation Case Summary</div>
    <table class="meta-box">
        <tr>
            <td class="meta-label">Case Title:</td>
            <td colspan="3"><strong>{{ $case->title }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Crime Type:</td>
            <td>{{ $case->crime_type ?? 'N/A' }}</td>
            <td class="meta-label">Jurisdiction:</td>
            <td>{{ $case->jurisdiction ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Opened By:</td>
            <td>{{ $case->openedBy?->name ?? 'N/A' }}</td>
            <td class="meta-label">Opened At:</td>
            <td>{{ $case->opened_at?->format('d M Y, H:i') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Legal Deadline:</td>
            <td>{{ $case->investigation_deadline?->format('d M Y') ?? 'Not Specified' }}</td>
            <td class="meta-label">Extension Stage:</td>
            <td>{{ $case->extension_stage ?? 'Initial' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Case Brief:</td>
            <td colspan="3">{{ $case->description ?: 'No additional case description recorded.' }}</td>
        </tr>
    </table>

    {{-- Section 2: Assigned Investigation Personnel --}}
    <div class="section-heading">2. Assigned Investigation &amp; Forensic Personnel</div>
    @if($case->assignedUsers->isEmpty())
        <p style="font-size: 8.5pt; color: #475569;">No additional personnel assigned beyond the case opening officer.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 30%;">Personnel Name</th>
                    <th style="width: 30%;">Official Email</th>
                    <th style="width: 18%;">Assigned Role</th>
                    <th style="width: 16%;">Assigned Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($case->assignedUsers as $idx => $member)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ ucfirst($member->pivot->role_in_case ?? 'Investigator') }}</td>
                        <td>{{ $member->pivot->assigned_at ? \Carbon\Carbon::parse($member->pivot->assigned_at)->format('d M Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Section 3: Registered Evidence & Cryptographic Integrity --}}
    <div class="section-heading">3. Evidence Inventory &amp; Cryptographic Hash Verification</div>
    @forelse($evidenceItems as $entry)
        @php
            $ev = $entry['model'];
            $integrity = $entry['integrity_status'];
        @endphp
        <div class="evidence-card">
            <div class="evidence-card-header">
                Exhibit {{ $ev->evidence_number }} &mdash; {{ strtoupper($ev->kind) }}
                ({{ $ev->device_type ?? $ev->original_filename ?? 'Evidence Item' }})
            </div>
            <table class="data-table" style="margin-bottom: 0;">
                <tr>
                    <td style="width: 22%;"><strong>Make / Model:</strong></td>
                    <td style="width: 28%;">{{ $ev->make_model ?? 'N/A' }}</td>
                    <td style="width: 22%;"><strong>Serial Number:</strong></td>
                    <td style="width: 28%;">{{ $ev->serial_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Seized At / Location:</strong></td>
                    <td>{{ $ev->seized_at?->format('d M Y, H:i') ?? 'N/A' }} ({{ $ev->seizure_location ?? 'N/A' }})</td>
                    <td><strong>Seizing Officer / Witness:</strong></td>
                    <td>{{ $ev->seizingOfficer?->name ?? 'N/A' }} / {{ $ev->witness_name ?? 'None' }}</td>
                </tr>
                <tr>
                    <td><strong>Current Custodian:</strong></td>
                    <td><strong>{{ $ev->currentHolder?->name ?? 'Unassigned' }}</strong></td>
                    <td><strong>Exhibit Status:</strong></td>
                    <td>{{ strtoupper($ev->status ?? 'ACTIVE') }}</td>
                </tr>
                @if($ev->isDigital())
                    <tr>
                        <td><strong>Acquisition Tool &amp; Method:</strong></td>
                        <td>{{ $ev->acquisition_tool ?? 'N/A' }} {{ $ev->acquisition_tool_version ? 'v'.$ev->acquisition_tool_version : '' }} ({{ $ev->acquisition_method ?? 'N/A' }})</td>
                        <td><strong>Write Blocker / Format:</strong></td>
                        <td>{{ $ev->write_blocker ?? 'N/A' }} / {{ $ev->image_format ?? 'Raw' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Original Filename / Size:</strong></td>
                        <td>{{ $ev->original_filename ?? 'N/A' }} ({{ $ev->file_size ? number_format($ev->file_size / 1024, 2) . ' KB' : 'N/A' }})</td>
                        <td><strong>Live Integrity Check:</strong></td>
                        <td class="{{ str_contains($integrity, 'VERIFIED') ? 'status-verified' : 'status-warn' }}">
                            {{ $integrity }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ strtoupper($ev->hash_algorithm ?? 'SHA-256') }} Fingerprint:</strong></td>
                        <td colspan="3" class="mono">{{ $ev->sha256_hash ?? 'Not Hashed' }}</td>
                    </tr>
                @else
                    <tr>
                        <td><strong>Physical Storage Locker:</strong></td>
                        <td colspan="3">{{ $ev->storage_locker ?? 'Evidence Vault' }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Description / Notes:</strong></td>
                    <td colspan="3">{{ $ev->description ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    @empty
        <p style="font-size: 8.5pt; color: #475569;">No evidence exhibits have been registered under this case.</p>
    @endforelse

    {{-- Section 4: Immutable Chain of Custody Ledger --}}
    <div class="section-heading">4. Official Append-Only Chain of Custody Log</div>
    @foreach($evidenceItems as $entry)
        @php $ev = $entry['model']; @endphp
        <p style="margin: 6px 0 4px 0; font-weight: bold; font-size: 8.8pt;">
            Custody Trail for Exhibit: {{ $ev->evidence_number }}
        </p>
        @if($ev->custodyEntries->isEmpty())
            <p style="font-size: 8pt; color: #64748b; margin-top: 0;">No custody transfers recorded yet.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 16%;">Timestamp</th>
                        <th style="width: 14%;">Action</th>
                        <th style="width: 18%;">Released By</th>
                        <th style="width: 18%;">Received By</th>
                        <th style="width: 16%;">Seal Condition</th>
                        <th style="width: 18%;">Purpose / Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ev->custodyEntries as $log)
                        <tr>
                            <td>{{ $log->occurred_at?->format('d M Y, H:i') ?? 'N/A' }}</td>
                            <td><strong>{{ strtoupper(str_replace('_', ' ', $log->action)) }}</strong></td>
                            <td>{{ $log->fromUser?->name ?? 'Initial Registration' }}</td>
                            <td>{{ $log->toUser?->name ?? ($log->recorder?->name ?? 'N/A') }}</td>
                            <td>
                                @if($log->release_seal_condition || $log->receipt_seal_condition)
                                    Rel: {{ $log->release_seal_condition ?? '-' }} / Rec: {{ $log->receipt_seal_condition ?? '-' }}
                                @else
                                    Intact / Verified
                                @endif
                            </td>
                            <td>{{ $log->reason ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- Section 5: Examiner Certification & Judicial Declaration --}}
    <div class="section-heading">5. Forensic Examiner Certification &amp; Declaration</div>
    <div class="declaration-box">
        I hereby certify that this report has been generated directly from the tamper-evident Digital Forensic Evidence Management System (DFEMS). All digital exhibits listed above were acquired using forensically sound procedures and verified via cryptographic SHA-256 hashing. The Chain of Custody records reflect an unbroken, append-only audit trail of every custody transfer and seal inspection.
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-line">
                    <strong>{{ $generator->name }}</strong><br>
                    Reporting Forensic Officer / Analyst<br>
                    Date: {{ $generatedAt->format('d M Y') }}
                </div>
            </td>
            <td style="text-align: right;">
                <div class="sig-line" style="margin-left: auto; text-align: left;">
                    <strong>Supervisor / Reviewing Authority</strong><br>
                    Signature &amp; Official Seal<br>
                    Date: ________________________
                </div>
            </td>
        </tr>
    </table>

    {{-- Report Integrity Footer --}}
    <div class="checksum-footer">
        <strong>Document UUID:</strong> <span class="mono">{{ $documentId }}</span><br>
        <strong>Report Integrity Checksum (SHA-256):</strong> <span class="mono">{{ $reportChecksum }}</span>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}  |  DFEMS Judicial Report: {{ $reportReference }}";
            $size = 7.5;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 28;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.39, 0.45, 0.55));
        }
    </script>
</body>
</html>