<?php

namespace Wwwillian\JsonCrud\Events;

use Wwwillian\JsonCrud\Models\BaseModel;
use Illuminate\Queue\SerializesModels;

class BaseModelSaving
{
    use SerializesModels;

    public $model;

    /**
     * Create a new event instance.
     *
     * @param Wwwillian\JsonCrud\Models\BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }
}
