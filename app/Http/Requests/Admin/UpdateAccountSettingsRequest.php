<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use function is_array;

class UpdateAccountSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = json_decode($this->input('payload', '{}'), true);

        $this->merge(is_array($payload) ? $payload : []);
    }

    public function rules(): array
    {
        return [
            'accountImage' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'password' => ['nullable', 'string', Password::min(8)],
        ];
    }
}