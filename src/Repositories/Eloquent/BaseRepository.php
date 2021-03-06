<?php

namespace Wwwillian\JsonCrud\Repositories\Eloquent;

use Wwwillian\JsonCrud\Repositories\Eloquent\AbstractRepository;
use Wwwillian\JsonCrud\Repositories\Contracts\IJsonRepository;

class BaseRepository extends AbstractRepository implements
IJsonRepository
{
    protected $modelClass;
    protected $model;

    public function __construct()
    {
        $this->model = app()->make($this->modelClass);
    }

    public function frontend()
    {
        return $this->model->frontend();
    }

    public function views()
    {
        return $this->model->views();
    }

    public function tags()
    {
        return $this->model->tags();
    }

    public function sanitizers()
    {
        return $this->model->getSanitizers();
    }
}
