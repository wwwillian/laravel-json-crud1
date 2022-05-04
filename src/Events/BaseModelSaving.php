<?php

namespace ConnectMalves\JsonCrud\Events;

use ConnectMalves\JsonCrud\Models\BaseModel;
use Illuminate\Queue\SerializesModels;

class BaseModelSaving
{
    use SerializesModels;

    public $model;

    /**
     * Create a new event instance.
     *
     * @param ConnectMalves\JsonCrud\Models\BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }
}
