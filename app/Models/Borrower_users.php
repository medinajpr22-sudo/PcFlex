<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Borrower_users extends Authenticatable
{
    use HasFactory, Notifiable;
    public static $rules = [
      'name' => 'required',
      'last_name' => 'required',
      'type_identification' => 'required',
      'number_identification' => 'required|unique:borrower_users,number_identification|regex:/^[0-9]{3,}$/',
      'sex_user' => 'required',
      'gender_sex' => 'required',
      'roll' => 'required',
      
      
    ];

    protected $fillable = 
    [
        'name', 'last_name', 
        'type_identification',
        'number_identification', 
        'sex_user',
         'gender_sex', 
         'roll',
         'password',
         'status',
         'name_user',
         'lastname_user',
         'user_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Para autenticación por número de documento
    public function getAuthIdentifierName()
    {
        return 'number_identification';
    }

    public function contacts()
    {
        return $this->hasOne(Contacts::class, 'id_user_con', 'id');
    }

    public function address()
    {
        return $this->hasOne(Addresses::class, 'id_user_add', 'id');
    }

    public function prueba()
    {
        return $this->hasOne(Relationships::class, 'user_rel_id', 'id');
    }
    
    // Relación con Services (un usuario puede tener múltiples préstamos)
    public function services()
    {
        return $this->hasMany(Services::class, 'user_borrower_id');
    }

    public function indexCards()
    {
        return $this->belongsToMany(Index_cards::class, 'relationships', 'user_rel_id', 'index_card_id');
    }


   
    
}
