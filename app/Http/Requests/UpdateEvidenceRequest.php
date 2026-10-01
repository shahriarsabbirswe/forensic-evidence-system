<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Corrections to the metadata only.
 *
 * The file, its hash, the evidence number and the case it belongs to
 * are deliberately absent. Those are the values the whole system exists
 * to protect, and nothing should be able to change them through a form.
 */
class UpdateEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('evidence.edit');
    }

    public function rules(): array
    {
        return [
            'device_type'   => ['required', 'string', 'max:100'],
            'make_model'    => ['nullable', 'string', 'max:150'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'description'   => ['required', 'string', 'max:5000'],

            'source'             => ['required', 'string', 'max:255'],
            'seized_at'          => ['required', 'date', 'before_or_equal:now'],
            'seizure_location'   => ['required', 'string', 'max:255'],
            'seizing_officer_id' => ['required', 'exists:users,id'],
            'witness_name'       => ['nullable', 'string', 'max:150'],

            'storage_locker' => ['nullable', 'string', 'max:100'],

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
}
