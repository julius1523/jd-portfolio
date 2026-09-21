<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use function is_array;

class UpdateAboutContentRequest extends FormRequest
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
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*.category' => ['required', 'string', 'max:150'],
            'skills.*.skill' => ['required', 'array', 'min:1'],
            'skills.*.skill.*' => ['required', 'string', 'max:150'],
            'skills.*.icon' => ['nullable', 'string', 'max:150'],
            'randomFacts' => ['nullable', 'array'],
            'randomFacts.*.icon' => ['required', 'string', 'max:150'],
            'randomFacts.*.randomFact' => ['required', 'string'],
            'others' => ['nullable', 'array'],
            'others.title' => ['nullable', 'string', 'max:255'],
            'others.description' => ['nullable', 'string'],
            'others_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ];
    }
}