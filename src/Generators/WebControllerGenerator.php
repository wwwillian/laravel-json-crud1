<?php

namespace Wwwillian\JsonCrud\Generators;

use Wwwillian\JsonCrud\Generators\BaseGenerator;
use Wwwillian\JsonCrud\Supports\Stub;

class WebControllerGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getTemplateContents()
    {
        return (new Stub('/web_controller.stub', [
            'studlyModuleName' => $this->getStudlyModuleName(),
            'className'        => $this->getWebControllerClassName(),
            'namespace'        => $this->getWebNamespace(),
            'serviceClass'     => $this->getServiceClass(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Project')
        ]))->render(); 
    }

    public function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.webcontrollers') . '/';
        }

        return base_path('app/') . config('jsoncrud.paths.generators.webcontrollers'). '/';
    }

    public function getFileName()
    {
        return $this->getWebControllerClassName () . '.php';
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->module);
        }
        return config('jsoncrud.project.name', 'Project');
    }

    protected function getWebControllerClassName()
    {
        return studly_case($this->config->name);
    }

    protected function getWebNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) . '\\' . $this->getStudlyModuleName() . '\\' . str_replace('/', '\\', config('jsoncrud.paths.generators.webcontrollers'));
        }

        return 'App\\' . str_replace('/', '\\', config('jsoncrud.paths.generators.webcontrollers'));
    }

    protected function getServiceClass()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) . '\\' . $this->getStudlyModuleName() . '\\' . str_replace('/', '\\', config('jsoncrud.paths.generators.services')) . '\\' . $this->getWebControllerClassName() . "::class";
        }

        return 'App\\' . str_replace('/', '\\', config('jsoncrud.paths.generators.services')) . '\\' . $this->getWebControllerClassName() . "::class";
    }
}
