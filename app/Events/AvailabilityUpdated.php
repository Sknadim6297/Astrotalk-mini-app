<?php

namespace App\Events;

use App\Models\Astrologer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvailabilityUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $astrologer;
    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct(Astrologer $astrologer, array $status)
    {
        $this->astrologer = $astrologer;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('astrologer-availability'),
            new PrivateChannel('astrologer.' . $this->astrologer->id . '.availability'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'astrologer_id' => $this->astrologer->id,
            'astrologer_name' => $this->astrologer->name,
            'status' => $this->status,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'AvailabilityUpdated';
    }
}
