<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReservationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('bibliotecarios'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reservation.created';
    }

    public function broadcastWith(): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'user_name' => $this->reservation->user->name . ' ' . $this->reservation->user->last_name,
            'equipment_type' => $this->reservation->equipment_type,
            'message' => "Nueva reserva de {$this->reservation->user->name} para {$this->reservation->equipment_type}",
        ];
    }
}
