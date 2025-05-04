<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartVotingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
        ];
    }
}
