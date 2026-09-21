<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use function is_array;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = json_decode($this->input('payload', '{}'), true);

        if (!is_array($payload)) {
            $payload = [];
        }

        $this->merge($payload);
    }

    public function rules(): array
    {
        return [
            'systemLogo' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'systemName' => ['required', 'string', 'max:255'],
            'systemColor' => ['required', 'string', 'max:7'],
        ];
    }
}