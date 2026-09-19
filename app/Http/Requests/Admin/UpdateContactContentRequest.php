<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use function is_array;

class UpdateContactContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = json_decode($this->input('payload', '{}'), true);

        if (!is_array($payload)) {
            throw new HttpResponseException(
                response()->json(['message' => 'Invalid payload.'], 422)
            );
        }

        $this->merge($payload);
    }

    public function rules(): array
    {
        return [
            'profileImage' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'socials' => ['nullable', 'array'],
            'socials.*.name' => ['required', 'string', 'max:255'],
            'socials.*.linkUrl' => ['required', 'url'],
            'socials.*.icon' => ['required', 'string', 'max:100'],
        ];
    }
}