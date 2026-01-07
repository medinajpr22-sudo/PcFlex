<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Services extends Model
{
    protected $fillable = [
        'user_borrower_id', 
        'equipment_id', 
        'librarian_borrower_id', 
        'date_ser', 
        'status', 
        'environment_id',
        'loan_duration_hours',
        'expected_return_date',
        'reminder_sent',
        'overdue_alert_sent'
    ];

    public static $rules =
    [
        'user_borrower_id' => 'required',
        'equipment_id' => 'required',
        'environment_id' => 'required',
    ];
    public static $Rules =
     [
      'user_returner_id' => 'required',
      'equipment_id' => 'required'
     ];
    use HasFactory;

    public function users()
    {
      return $this->belongsTo(Borrower_users::class, 'user_borrower_id');
    }
  
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
   
    public function environment()
  {
      return $this->belongsTo(Environments::class, 'environment_id');
  }
  public function librarian()
  {
    return $this->belongsTo(user::class, 'librarian_borrower_id');

  }
  public function librarianreturn()
  {
    return $this->belongsTo(user::class, 'librarian_returner_id');
  }
}
