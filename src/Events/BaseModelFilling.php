<?php

namespace Wwwillian\JsonCrud\Events;

use Wwwillian\JsonCrud\Models\BaseModel;
use Illuminate\Queue\SerializesModels;

class BaseModelFilling
{
    use SerializesModels;

    public $model;
    public $attributes;

    /**
     * Create a new event instance.
     *
     * @param Wwwillian\JsonCrud\Models\BaseModel $model
     */
    public function __construct(BaseModel $model, $attributes)
    {
        $this->model = $model;
        $this->attributes = $attributes;
    }
}
