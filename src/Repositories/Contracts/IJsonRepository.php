<?php

namespace ConnectMalves\JsonCrud\Repositories\Contracts;

interface IJsonRepository extends IRepository
{
    public function frontend();
    public function views();
    public function tags();
}
