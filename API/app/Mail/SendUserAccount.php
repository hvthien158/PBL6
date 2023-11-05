<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class SendUserAccount extends Mailable
{
    use Queueable, SerializesModels;
    protected $account;
    
    /**
     * Create a new message instance.
     */
    public function __construct($account)
    {
        $this->account = $account
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        
        return new Envelope(
            subject: 'Welcome to '.$this->account['name'].' come to company',
        );
    }

    /**
     * Get the message content definition.
     */
public function content(): Content
    {
        return new Content(
            view: 'view.mail.mail_account',
            with: [
                'email' : $this->account['email'],
                'password': $this->account['password'],
                'link': $this->account['link'],
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
