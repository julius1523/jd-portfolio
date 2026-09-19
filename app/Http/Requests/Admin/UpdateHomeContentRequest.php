<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use function is_array;

class UpdateHomeContentRequest extends FormRequest
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
            'subheading' => ['nullable', 'array', 'min:1', 'max:4'],
            'subheading.*' => ['string'],
            'description' => ['nullable', 'string'],
            'primaryBtnText' => ['nullable', 'string', 'max:255'],
            'primaryBtnLink' => ['nullable', 'string', 'max:255'],
            'secondaryBtnText' => ['nullable', 'string', 'max:255'],
            'secondaryBtnFile' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}