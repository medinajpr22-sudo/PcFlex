<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationApproved implements ShouldBroadcast
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
            new PrivateChannel('user.' . $this->reservation->user_borrower_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reservation.approved';
    }

    public function broadcastWith(): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'equipment_serie' => $this->reservation->equipment->serie_equi ?? 'N/A',
            'equipment_type' => $this->reservation->equipment_type,
            'message' => "¡Tu reserva ha sido aprobada! Equipo {$this->reservation->equipment->serie_equi} está listo para recoger",
        ];
    }
}
