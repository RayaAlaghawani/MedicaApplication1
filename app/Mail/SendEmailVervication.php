<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// app/Mail/SendEmailVervication.php

class SendEmailVervication extends Mailable
{
    use Queueable, SerializesModels;

    public $verification; // ← هنا تم التصحيح من verfication إلى verification

    public function __construct($verification)
    {
        $this->verification = $verification;
    }

    public function build()
    {
        return $this->markdown('emails.send-code-email-verification');
    }
}
