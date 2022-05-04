<?php

namespace Wwwillian\JsonCrud\Services;

use Wwwillian\JsonCrud\Services\BaseService;

class Service extends BaseService 
{
    protected $repositoryClass = Wwwillian\JsonCrud\Repositories\Eloquent\Repository::class;
}
