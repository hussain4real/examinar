<?php

namespace App\Events;

use App\Models\ExamSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamSessionEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ExamSession $examSession) {}

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        return [
            new Channel('exam-session.'.$this->examSession->id),
            new Channel('lobby'),
        ];
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->examSession->id,
            'status' => 'completed',
        ];
    }
}
