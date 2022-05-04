<?php

namespace ConnectMalves\JsonCrud\Services;

use ConnectMalves\JsonCrud\Services\BaseService;

class Service extends BaseService 
{
    protected $repositoryClass = ConnectMalves\JsonCrud\Repositories\Eloquent\Repository::class;
}
