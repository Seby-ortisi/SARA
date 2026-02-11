<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dataView; // pubblica se vuoi accedere direttamente dalla view

    public function __construct($dataView)
    {
        $this->dataView = $dataView;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.test', // nome della view in dot notation
            with: [
                'dataView' => $this->dataView // passa i dati alla view
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
