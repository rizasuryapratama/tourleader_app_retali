<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportMasukMail extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $pesan;
    public $subjectEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($username, $pesan, $subjectEmail)
    {
        $this->username = $username;
        $this->pesan = $pesan;
        $this->subjectEmail = $subjectEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.report_masuk',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
