<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAccountSettingsRequest;
use App\Services\FileUploadService;

class AccountSettingsController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function getAccountSettings()
    {
        $user = auth()->user();

        return response()->json($this->transform($user));
    }

    public function updateAccountSettings(UpdateAccountSettingsRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        $user->fill([
            'first_name' => $validated['firstName'] ?? $user->first_name,
            'last_name' => $validated['lastName'] ?? $user->last_name,
            'email' => $validated['email'] ?? $user->email,
        ]);

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $this->fileUploadService->handle($request, $user, 'accountImage', 'account_image', 'uploads/images');

        $user->save();

        return response()->json([
            'message' => 'Changes successfully saved.',
            'data' => $this->transform($user),
        ]);
    }

    private function transform($user): array
    {
        return [
            'accountImage' => $user->account_image,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'email' => $user->email,
        ];
    }
}