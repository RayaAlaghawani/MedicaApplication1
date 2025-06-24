<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendrejectJoinRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */


    public $doctorName;
    public $rejectionMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($doctorName, $rejectionMessage)
    {
        $this->doctorName = $doctorName;
        $this->rejectionMessage = $rejectionMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رفض طلب الانضمام',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.join_rejected',
            with: [
                'doctorName' => $this->doctorName,
                'rejectionMessage' => $this->rejectionMessage,
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
