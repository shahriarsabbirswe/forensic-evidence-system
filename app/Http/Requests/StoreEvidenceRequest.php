<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('evidence.register');
    }

    public function rules(): array
    {
        return [
            // What it is
            'kind'          => ['required', 'in:digital,physical'],
            'device_type'   => ['required', 'string', 'max:100'],
            'make_model'    => ['nullable', 'string', 'max:150'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'description'   => ['required', 'string', 'max:5000'],

            // Seizure metadata (FR2)
            'source'             => ['required', 'string', 'max:255'],
            'seized_at'          => ['required', 'date', 'before_or_equal:now'],
            'seizure_location'   => ['required', 'string', 'max:255'],
            'seizing_officer_id' => ['required', 'exists:users,id'],
            'witness_name'       => ['nullable', 'string', 'max:150'],

            // Digital only
            'evidence_file' => ['required_if:kind,digital', 'nullable', 'file', 'max:102400'],

            // Physical only
            'storage_locker' => ['required_if:kind,physical', 'nullable', 'string', 'max:100'],

            // Acquisition details
            'source_state'             => ['nullable', 'string', 'max:150'],
            'write_blocker'            => ['nullable', 'string', 'max:150'],
            'acquisition_method'       => ['nullable', 'in:live,dead'],
            'acquisition_scope'        => ['nullable', 'in:physical,logical'],
            'image_format'             => ['nullable', 'in:raw,e01,aff4,ad1,other'],
            'acquisition_tool'         => ['nullable', 'string', 'max:100'],
            'acquisition_tool_version' => ['nullable', 'string', 'max:50'],
            'acquired_by'              => ['nullable', 'exists:users,id'],
            'acquisition_started_at'   => ['nullable', 'date'],
            'acquisition_completed_at' => ['nullable', 'date', 'after_or_equal:acquisition_started_at'],
            'acquisition_timezone'     => ['nullable', 'string', 'max:64'],
            'acquisition_notes'        => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'evidence_file.required_if'  => 'Digital evidence needs a file.',
            'storage_locker.required_if' => 'Physical evidence needs a storage locker reference.',
            'seized_at.before_or_equal'  => 'Evidence cannot be seized in the future.',
            'evidence_file.max'          => 'The file is larger than 100 MB. Raise upload_max_filesize in php.ini if this is a real image.',
            'acquisition_completed_at.after_or_equal' => 'Acquisition cannot finish before it started.',
        ];
    }
}
