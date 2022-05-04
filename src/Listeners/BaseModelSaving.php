<?php

namespace ConnectMalves\JsonCrud\Listeners;

use ConnectMalves\JsonCrud\Events\BaseModelSaving as BaseModelSavingEvent;

class BaseModelSaving
{
    /**
     * Handle the event.
     *
     * @param  ConnectMalves\JsonCrud\Events\ModelSavingEvent $event
     * @return mixed
     */
    public function handle(BaseModelSavingEvent $event)
    {
        $model = $event->model;
        
        foreach ($model->casts() as $key => $value) {
            if ($value == 'boolean') {
                $model[$key] == 'true' || $model[$key] == true || $model[$key] == 'on' ?
                    $model[$key] = 1 : $model[$key] = 0;
            }
        }
    }
}
