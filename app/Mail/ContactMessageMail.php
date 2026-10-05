<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\SystemSettings;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contact->email, $this->contact->name)],
            subject: $this->contact->subject
            ?: 'New portfolio message from ' . $this->contact->name,
        );
    }

    public function content(): Content
    {
        $settings = SystemSettings::whereIn('key', ['system_color', 'system_name'])
            ->pluck('value', 'key');
        $accent = $settings['system_color'] ?? '#14429F';
        [$r, $g, $b] = sscanf($accent, '#%02x%02x%02x');
        $css = file_get_contents(resource_path('css/email.css'));
        $html = view('emails.contact-message', [
            'contact' => $this->contact,
            'accent' => $accent,
            'tint' => "rgba($r, $g, $b, 0.1)",
            'siteName' => $settings['system_name'] ?? config('app.name'),
        ])->render();

        return new Content(
            htmlString: (new CssToInlineStyles())->convert($html, $css),
        );
    }
}