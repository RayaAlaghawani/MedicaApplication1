<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class SendapproveJoinRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $doctorName;

    /**
     * Create a new message instance.
     */
    public function __construct($message, $doctorName)
    {
        $this->message = $message;
        $this->doctorName = $doctorName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تمت الموافقة على طلب الانضمام',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.join_approved',
            with: [
                'messageContent' => $this->message,
                'doctorName' => $this->doctorName,
            ]
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
