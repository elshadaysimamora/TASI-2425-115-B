<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingConfiguration extends Model
{
    /** @use HasFactory<\Database\Factories\RatingConfigurationFactory> */
    use HasFactory;
    protected $fillable = ['name', 'min_scale', 'max_scale', 'is_active'];

    //cast is_active to boolean
    protected $casts = [
        'is_active' => 'boolean',
    ];

    //relationship to Rating
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    //get active rating configuration
    public static function getActive(){
        return static::where('is_active', true)->first();
    }
}
