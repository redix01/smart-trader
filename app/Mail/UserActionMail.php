<?php

namespace App\Mail;

use App\Services\PlatformSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserActionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $heading,
        public string $messageLine,
        public array $details = [],
        public ?string $actionLabel = null,
        public ?string $actionUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $settings = app(PlatformSettingsService::class);

        return new Envelope(
            from: new Address((string) config('mail.from.address'), $settings->getMailFromName()),
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-action',
            text: 'emails.user-action-text',
            with: ['brandName' => app(PlatformSettingsService::class)->getSiteName()],
        );
    }
}
