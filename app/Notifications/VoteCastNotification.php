<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VoteCastNotification extends Notification
{
    use Queueable;

    protected $voterId;
    protected $voterName;
    protected $session;

    /**
     * @param string $voterId   National ID (plain)
     * @param string $voterName Full name
     * @param \App\Models\VotingSession $session
     */
    public function __construct(string $voterId, string $voterName, $session)
    {
        $this->voterId   = $voterId;
        $this->voterName = $voterName;
        $this->session   = $session;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message'    => "رأی ثبت شد: {$this->voterName} ({$this->voterId})",
            'session_id' => $this->session->id,
            'voter_id'   => $this->voterId,
            'voter_name' => $this->voterName,
        ];
    }
}
