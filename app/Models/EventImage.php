<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventImage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'event_id', 'user_id', 'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
