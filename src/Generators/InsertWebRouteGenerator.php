<?php

namespace Wwwillian\JsonCrud\Generators;

use Wwwillian\JsonCrud\Generators\BaseGenerator;
use Wwwillian\JsonCrud\Supports\Stub;

class InsertWebRouteGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config   = $config;
    }

    protected function getTemplateContents()
    {
        $newRoute = (new Stub('/route.stub', [
            'controllerName'    => $this->getControllerName(),
            'routeName'         => $this->getRouteName(),
            'routes'            => $this->getRoutes(),
            'name'              => '',
            'type'              => 'resource'
        ]))->render();

        return file_get_contents($this->getDestinationDirectory().$this->getFileName()) . "\n" . $newRoute;
    }

    protected function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule')) {
            return base_path(config('jsoncrud.paths.modules')) .'/'. $this->getStudlyModuleName().'/';
        }
        return base_path() . '/';
    }

    protected function getFileName()
    {
        return config('jsoncrud.files.webroutes');
    }

    protected function getControllerName()
    {
        return $this->config->authenticatable == true ? 'DashboardController' : studly_case($this->config->controller);
    }

    protected function getRouteName()
    {
        return snake_case($this->config->route);
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

        $only = str_replace(',', ', ', isset($config->only) ? $config->only : "");
        $except = str_replace(',', ', ', isset($config->except) ? $config->except : "");
        
        if ($only != "") {
            return "->only(['". $only . "'])";
        }

        if ($except != "") {
            return "->except(['". $except . "'])";
        }

        return '';
    }
}
