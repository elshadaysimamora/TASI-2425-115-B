<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'message_id', 'rating_configuration_id', 'rating'];

    //relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relationship to Message
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    //relationship to RatingConfiguration
    public function ratingConfiguration()
    {
        return $this->belongsTo(RatingConfiguration::class, 'rating_configuration_id');
    }
}
