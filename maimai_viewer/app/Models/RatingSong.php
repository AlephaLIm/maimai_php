<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingSong extends Model
{
    protected $table = 'songs';

    public function charts()
    {
        return $this->hasMany(RatingChart::class, 'parentsong');
    }
}