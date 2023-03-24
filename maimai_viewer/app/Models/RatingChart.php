<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingChart extends Model
{
    protected $table = 'charts';

    public function song()
    {
        return $this->belongsTo(RatingSong::class, 'parentsong');
    }

    public function scores()
    {
        return $this->hasMany(RatingScore::class, 'chartid');
    }
}