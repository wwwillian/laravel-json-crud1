<?php

namespace Wwwillian\JsonCrud\Generators;

use Wwwillian\JsonCrud\Generators\BaseGenerator;
use Wwwillian\JsonCrud\Supports\Stub;

class InsertApiRouteGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config   = $config;
    }

    protected function getTemplateContents()
    {
        $newRoute = (new Stub('/api_route.stub', [
            'controllerName'     => $this->getControllerName(),
            'routeName'          => $this->getRouteName(),
            'routes'             => $this->getRoutes()
        ]))->render();

        return file_get_contents( $this->getDestinationDirectory() . $this->getFileName()) . "\n" . $newRoute;
    }

    protected function getControllerName()
    {
        return studly_case($this->config->controller);
    }

    protected function getRouteName()
    {
        return snake_case($this->config->route);
    }

    protected function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return base_path(config('jsoncrud.paths.modules')) .'/'. $this->getStudlyModuleName().'/';
        }
        return base_path() . '/';
    }

    protected function getFileName()
    {
        return config('jsoncrud.files.apiroutes');
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->module);
        }
        return config('jsoncrud.project.name', 'Project');
    }

    protected function getRoutes()
    {
        $config = $this->config;
        
        $regex = ['(edit(,?)|create(,?))', ','];
        $replaces = ['', ', '];

        $only = str_replace($regex, $replaces, isset($config->only) ? $config->only : "");
        $except = str_replace($regex, $replaces, isset($config->except) ? $config->except : "");

        if ($only != "") {
            return "->only(['". $only . "'])";
        }

        if ($except != "") {
            return "->except(['". $except . "'])";
        }

        return '';
    }
}
