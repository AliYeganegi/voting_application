<?php

namespace App\Notifications;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Attributes\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log as FacadesLog;

class VoteCastNotification extends Notification
{
    use Queueable;

    protected string $voterId;
    protected string $voterName;
    protected int    $sessionId;

    public function __construct(string $voterId, string $voterName, int $sessionId)
    {
        $this->voterId   = $voterId;
        $this->voterName = $voterName;
        $this->sessionId = $sessionId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $queue = \App\Models\Verification::query()
            ->where('voting_session_id', $this->sessionId)
            ->where('status', 'pending')
            ->with('voter:id,voter_id,first_name,last_name')
            ->get()
            ->filter(fn($v) => $v->voter !== null)
            ->map(fn($v) => [
                'name' => "{$v->voter->first_name} {$v->voter->last_name}",
                'id'   => $v->voter->voter_id,
            ])
            ->toArray();

        return [
            'type'       => 'vote_cast',
            'voter_name' => $this->voterName,
            'voter_id'   => $this->voterId,
            'session_id' => $this->sessionId,
            'queue'      => $queue,
        ];
    }
}
