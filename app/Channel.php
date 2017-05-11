<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    // This is so {channel} in Routes return SLUG instead of ID
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
