<?php

namespace App\Mail;

use App\Models\Tournament;
use App\Models\TournamentJoin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class TournamentJoinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tournament $tournament;
    public TournamentJoin $join;
    public string $joinCode;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Tournament $tournament,
        TournamentJoin $join,
        string $joinCode
    ) {
        $this->tournament = $tournament;
        $this->join = $join;
        $this->joinCode = $joinCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎮 Tournament Joined Successfully – Join Code Inside'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tournament-joined',
            with: [
                'tournament' => $this->tournament,
                'join'       => $this->join,
                'joinCode'   => $this->joinCode,
            ],
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
