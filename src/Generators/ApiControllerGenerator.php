<?php

namespace ConnectMalves\JsonCrud\Generators;

use ConnectMalves\JsonCrud\Generators\BaseGenerator;
use ConnectMalves\JsonCrud\Supports\Stub;

class ApiControllerGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getTemplateContents()
    {
        return (new Stub('/api_controller.stub', [
            'studlyModuleName'      => $this->getStudlyModuleName(),
            'className'             => $this->getClassName(),
            'namespace'             => $this->getNamespace(),
            'serviceClass'          => $this->getServiceClass(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Project')
        ]))->render();
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->module);
        }
        return config('jsoncrud.project.name', 'Project');
    }

    protected function getDestinationDirectory()
    { 
        if (config('jsoncrud.hasmodule', false)) {
            return base_path( config('jsoncrud.paths.modules') ) .'/' . $this->getStudlyModuleName().'/'. config('jsoncrud.paths.generators.apicontrollers').'/';
        }

        return base_path('app/') . config('jsoncrud.paths.generators.apicontrollers') . '/';
    }

    public function getFileName()
    {
        return $this->getClassName() . '.php';
    }

    protected function getClassName()
    {
        return studly_case($this->config->name);
    }

    protected function getNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. str_replace('/', '\\', config('jsoncrud.paths.generators.apicontrollers'));
        }

        return 'App\\' . str_replace('/', '\\', config('jsoncrud.paths.generators.apicontrollers'));
    }

    protected function getServiceClass()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. str_replace('/', '\\', config('jsoncrud.paths.generators.services')) . '\\'.$this->getClassName()."::class";
        }

        return 'App\\' . str_replace('/', '\\', config('jsoncrud.paths.generators.services')) . '\\' . $this->getClassName() . "::class";
    }
}
