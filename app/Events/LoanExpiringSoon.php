<?php

namespace App\Events;

use App\Models\Services;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanExpiringSoon implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $service;
    public $hoursRemaining;

    public function __construct(Services $service, $hoursRemaining)
    {
        $this->service = $service;
        $this->hoursRemaining = $hoursRemaining;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('bibliotecarios'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'loan.expiring';
    }

    public function broadcastWith(): array
    {
        return [
            'service_id' => $this->service->id,
            'user_name' => $this->service->users->name . ' ' . $this->service->users->last_name,
            'equipment_serie' => $this->service->equipment->serie_equi,
            'hours_remaining' => $this->hoursRemaining,
            'message' => "El préstamo de {$this->service->users->name} vence en {$this->hoursRemaining} hora(s)",
        ];
    }
}
