<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'phone' => ['nullable', 'digits_between:1,11'],
            'email' => ['nullable', 'email', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.digits_between' => 'The phone number must contain digits only and be no longer than 11 digits.',
            'email.max' => 'The email may not be longer than 30 characters.',
            'name.max' => 'The name may not be longer than 20 characters.',
        ];
    }
}
