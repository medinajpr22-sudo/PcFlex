<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_borrower_id',
        'equipment_type',
        'reservation_date',
        'desired_date',
        'status',
        'notes',
        'equipment_id',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
        'desired_date' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public static $rules = [
        'user_borrower_id' => 'required',
        'equipment_type' => 'required|string',
    ];

    // Relación con el usuario que hace la reserva
    public function user()
    {
        return $this->belongsTo(Borrower_users::class, 'user_borrower_id');
    }

    // Relación con el equipo asignado
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    // Relación con el bibliotecario que aprobó
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
