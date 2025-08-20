<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecretaryWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $doctorName;

    public function __construct($email, $password, $doctorName)
    {
        $this->email = $email;
        $this->password = $password;
        $this->doctorName = $doctorName;
    }

    public function build()
    {
        return $this->subject('مرحباً بك في فريق العيادة')
            ->view('emails.secretary_welcome');
    }
}
