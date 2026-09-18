<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = json_decode($this->input('payload', '{}'), true) ?? [];

        $this->merge([
            'systemName' => $payload['systemName'] ?? null,
            'systemColor' => $payload['systemColor'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'systemName' => ['required', 'string', 'max:255'],
            'systemColor' => ['required', 'string', 'max:7'],
            'systemLogo' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ];
    }
}