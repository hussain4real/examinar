<?php

namespace App\Events;

use App\Models\ExamAttempt;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentKicked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ExamAttempt $attempt) {}

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        return [
            new Channel('exam-session.'.$this->attempt->exam_session_id),
        ];
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->attempt->exam_session_id,
            'user_id' => $this->attempt->user_id,
        ];
    }
}
