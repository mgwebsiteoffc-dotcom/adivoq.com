<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\TeamMember;

class TeamInviteMail extends Mailable
{
    public $teamMember;
    public $inviteUrl;

public function __construct(TeamMember $teamMember)
{
    $this->teamMember = $teamMember;
    $this->inviteUrl = url('/accept-invite/' . $teamMember->invite_token);
}

    public function build()
    {
        return $this->subject('You are invited to join AdivoQ')
            ->view('emails.team-invite')
            ->with([
                'inviteUrl' => $this->inviteUrl,
                'teamMember' => $this->teamMember,
            ]);
    }
}