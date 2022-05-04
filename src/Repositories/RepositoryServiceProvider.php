<?php

namespace Wwwillian\JsonCrud\Repositories;

use Wwwillian\JsonCrud\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        BaseRepository::getRelationsResolver(function($relations = 'get_relations') {
            $getRelations = $this->app['request']->input($relations);
            $response = filter_var($getRelations, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            return isset($getRelations) && $response !== NULL ?  $response : true;
        });
    }
}
