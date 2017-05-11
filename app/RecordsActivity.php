<?php

namespace App;

trait RecordsActivity
{

    // Same as boot method on the model itself
    // name must be bootCamelCase
    protected static function bootRecordsActivity()
    {
        if(auth()->guest()) return ;

        foreach(static::getRecordEvents() as $event){
            // Create new record when a thread or reply or aything is created
            // Any model using this will have activities associated
            static::$event(function($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function($model) {
            $model->activity()->delete();
        });
    }

    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event)
        ]);
    }

    protected static function getRecordEvents()
    {
        // What event types we want an activity created for
        // 'created', 'deleted', 'udpdated', etc...
        // From HasEvents.php
        return ['created'];
    }

    public function activity()
    {
        // This will fill subject_type and subject_id
        return $this->morphMany('App\Activity', 'subject');
    }

    public function getActivityType($event)
    {
        // ReflectionClass return info about a class
        // Will return shortened class name without the namespace
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}
