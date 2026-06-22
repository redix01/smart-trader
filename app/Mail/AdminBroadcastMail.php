<?php

namespace App\Mail;

use App\Helpers\WebsiteSettingsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Address as SymfonyAddress;
use Symfony\Component\Mime\Email;

class AdminBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $mailData)
    {
    }

    public function envelope(): Envelope
    {
        $fromEmail = $this->mailData['from_email'];
        $fromName = $this->mailData['from_name'] ?? WebsiteSettingsHelper::getSiteName();

        return new Envelope(
            subject: $this->mailData['subject'],
            from: new Address($fromEmail, $fromName),
            replyTo: [
                new Address($fromEmail, $fromName),
            ],
            using: [
                function (Email $message) use ($fromEmail, $fromName): void {
                    $address = new SymfonyAddress($fromEmail, $fromName);

                    $message->from($address);
                    $message->sender($address);
                    $message->replyTo($address);
                },
            ],
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
