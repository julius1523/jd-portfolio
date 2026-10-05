<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = Contact::create($validated);

        $recipient = User::query()->orderBy('id')->value('email');

        try {
            Mail::to($recipient)->send(new ContactMessageMail($contact));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Message sent successfully.'
        ]);
    }
}