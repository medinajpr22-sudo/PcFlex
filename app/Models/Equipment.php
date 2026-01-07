<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
    static $rules = [
        'type_equi' => 'required',
        'characteristics' => 'required',
        'serie_equi' => 'required|unique:equipment,serie_equi|regex:/^[0-9]{3,}$/',
    ];

  
    protected $fillable = ['type_equi', 'serie_equi', 'characteristics', 'states', 'name_equi', 'status'];
  
    // Relación con Services (un equipo puede tener múltiples préstamos)
    public function services()
    {
        return $this->hasMany(Services::class, 'equipment_id');
    }
    
    // Relación individual con el último servicio
    public function service()
    {
        return $this->belongsTo(Services::class);
    }
}
