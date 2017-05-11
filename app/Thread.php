<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filter\ThreadFilters;
use App\Activity;

class Thread extends Model
{

    use RecordsActivity;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount', function($builder) {
            $builder->withCount('replies');
        });

        // Delete replies when deleting a thread
        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });

    }

    public function path()
    {
        return "/{$this->channel->slug}/{$this->id}";
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
