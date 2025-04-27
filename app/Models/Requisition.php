<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'details',
        'is_gift',
        'is_claimed',
        'claimed_by',
        'created_by',
    ];

  
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
}
