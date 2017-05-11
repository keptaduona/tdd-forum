<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    public function subject()
    {
        /* Without passing a name
           morphTo() will use the function name to determine
           what the _type and _id columns are called
        */
        return $this->morphTo();
    }

    protected static function feed($user, $take = 50)
    {
        // activity() defined in User::class
        // This will group activities by date
        return static::where('user_id', $user->id)
            ->latest()
            ->with('subject')
            ->take($take)
            ->get()
            ->groupBy(function($activity) {
                return $activity->created_at->format('Y-m-d');
        });
    }
}
