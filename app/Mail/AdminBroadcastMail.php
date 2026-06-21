<?php

namespace App\Mail;

use App\Helpers\WebsiteSettingsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $mailData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailData['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-broadcast',
            with: [
                'subjectLine' => $this->mailData['subject'],
                'messageBody' => $this->mailData['message'],
                'headerColor' => $this->mailData['header_color'],
                'accentLabel' => $this->mailData['accent_label'] ?? null,
                'footerNote' => $this->mailData['footer_note'] ?? null,
                'recipientEmail' => $this->mailData['recipient_email'] ?? null,
                'siteName' => WebsiteSettingsHelper::getSiteName(),
                'siteTagline' => WebsiteSettingsHelper::getSiteTagline(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
