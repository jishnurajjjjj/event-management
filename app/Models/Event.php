<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title', 'date', 'time', 'type', 'guidelines', 'user_id', 'event_for_id'
    ];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'date' => 'date', 
    
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'event_for_id');
    }
    public function invitations() {
        return $this->hasMany(EventInvitation::class);
    }
    
    public function invitedUsers() {
        return $this->belongsToMany(User::class, 'event_invitations')
                   ->withPivot('status')
                   ->withTimestamps();
    }
    public function hasPendingInvitationForUser($userId = null)
    {
        return $this->invitations()
                   ->where('user_id', $userId ?? auth()->id())  
                   ->where('status', 'pending')
                   ->exists(); 
    }

public function images()
{
    return $this->hasMany(EventImage::class);
}
public function requisitions()
{
    return $this->hasMany(Requisition::class);
}
}
