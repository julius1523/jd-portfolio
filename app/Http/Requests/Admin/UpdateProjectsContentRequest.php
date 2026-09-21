<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function is_array;

class UpdateProjectsContentRequest extends FormRequest
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
            'profileImage' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'projects' => ['nullable', 'array'],
            'projects.*.category' => [
                'required',
                'string',
                Rule::in([
                    'Software Development',
                    'Technical Documentation',
                    'Presentations/Multimedia',
                ])
            ],
            'projects.*.name' => ['required', 'string', 'max:255'],
            'projects.*.description' => ['required', 'string'],
            'projects.*.materials' => ['required', 'array', 'min:1'],
            'projects.*.materials.*' => ['string', 'max:100'],

            'projects.*.linkType' => ['required', 'in:upload,link'],
            'projects.*.linkUrl' => ['nullable', 'url', 'required_if:projects.*.linkType,link'],

            'projectImages' => ['nullable', 'array'],
            'projectImages.*' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
            'projectLinkFiles' => ['nullable', 'array'],
            'projectLinkFiles.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip', 'max:20480'],
        ];
    }
}