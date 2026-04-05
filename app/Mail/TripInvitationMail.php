<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TripInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $trip;
    public $inviter;
    public $token;

    public function __construct($trip, $inviter, $token)
    {
        $this->trip = $trip;
        $this->inviter = $inviter;
        $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '邀請您共同規劃旅程：' . $this->trip->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trip_invitation',
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
