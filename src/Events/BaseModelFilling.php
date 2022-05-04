<?php

namespace ConnectMalves\JsonCrud\Events;

use ConnectMalves\JsonCrud\Models\BaseModel;
use Illuminate\Queue\SerializesModels;

class BaseModelFilling
{
    use SerializesModels;

    public $model;
    public $attributes;

    /**
     * Create a new event instance.
     *
     * @param ConnectMalves\JsonCrud\Models\BaseModel $model
     */
    public function __construct(BaseModel $model, $attributes)
    {
        $this->model = $model;
        $this->attributes = $attributes;
    }
}
