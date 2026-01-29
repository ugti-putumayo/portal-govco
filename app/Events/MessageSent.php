<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn()
    {
        $sender   = $this->message->sender_id;
        $receiver = $this->message->receiver_id;

        return [
            new PrivateChannel("chat.{$sender}.{$receiver}"),
            new PrivateChannel("chat.{$receiver}.{$sender}"),
            new PrivateChannel("user.{$receiver}"),
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}