<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Email pemberitahuan ke user yang akunnya dihapus admin, berisi alasan penghapusan.
class AccountDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $reason,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Akun Parfum Finder kamu telah dihapus');
    }

    public function content(): Content
    {
        // Property public di atas otomatis tersedia di view ($recipientName, $reason).
        return new Content(view: 'emails.account-deleted');
    }
}
