<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingScore extends Model
{
    protected $table = 'scores';

    public function chart()
    {
        return $this->belongsTo(RatingChart::class, 'chartid');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'friendcode');
    }
}