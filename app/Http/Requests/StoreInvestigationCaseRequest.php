<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestigationCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('case.create');
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'crime_type'   => ['required', 'string', 'max:100'],
            'jurisdiction' => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'status'       => ['required', 'in:open,under_investigation,closed,archived'],
            'opened_at'    => ['required', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'opened_at.before_or_equal' => 'A case cannot be opened with a future date.',
        ];
    }
}
