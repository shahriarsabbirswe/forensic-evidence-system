<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEvidenceTransferRequest extends FormRequest
{
    /**
     * Who may request a transfer is decided in the controller
     * (permission plus "must be the current holder").
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $evidence = $this->route('evidence');

        return [
            'to_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([$evidence?->current_holder_id]),
            ],
            'purpose' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_user_id.not_in' => 'The item is already held by this person. Choose someone else.',
            'purpose.min' => 'Please describe the purpose in at least 10 characters.',
        ];
    }
}
