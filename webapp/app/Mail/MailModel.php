<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;


class MailModel extends Mailable
{
    public $mailData;

    public function __construct($mailData) {
        $this->mailData = $mailData;
    }

    public function envelope() {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'no-reply@flick.com'), env('MAIL_FROM_NAME', 'Flick Support')),
            subject: 'Recover Password',
        );
    }

    public function content() {
        return new Content(
            view: 'emails.mail',    // Update to the correct view file path
            with: $this->mailData,  // Pass data to the view
        );
    }
}
