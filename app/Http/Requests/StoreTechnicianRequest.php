<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'specialty' => ['nullable', 'string', 'max:20', 'not_regex:/[0-9]/'],
            'phone' => ['nullable', 'digits_between:1,11'],
            'email' => ['nullable', 'email', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.digits_between' => 'The phone number must contain digits only and be no longer than 11 digits.',
            'specialty.max' => 'The specialty may not be longer than 20 characters.',
            'specialty.not_regex' => 'The specialty may not contain numbers.',
            'email.max' => 'The email may not be longer than 30 characters.',
            'name.max' => 'The name may not be longer than 20 characters.',
        ];
    }
}
