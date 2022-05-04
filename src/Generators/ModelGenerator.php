<?php

namespace ConnectMalves\JsonCrud\Generators;

use ConnectMalves\JsonCrud\Generators\BaseGenerator;
use ConnectMalves\JsonCrud\Supports\Stub;

class ModelGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config   = $config;
    }

    public function getTemplateContents()
    {
        $config = $this->config;
        return (new Stub($config->authenticatable == true ? '/authenticatable_model.stub' : '/model.stub', [
            'studlyModuleName'      => $this->getStudlyModuleName(),
            'className'             => $this->getClassName(),
            'namespace'             => $this->getNamespace(),
            'jsonFileName'          => $this->getJsonFileName(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Project'),
        ]))->render();
    }

    public function getFileName()
    {
       return $this->getClassName() . '.php';
    }

    public function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return base_path(config('jsoncrud.paths.modules'))  . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.models') . '/';
        }
        return base_path('app/') . config('jsoncrud.paths.generators.models') . '/' ;
    }

    protected function getClassName()
    {
        return studly_case($this->config->name);
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->module);
        }
        return config('jsoncrud.project.name', 'Project');
    }

    protected function getNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. config('jsoncrud.paths.generators.models');
        }
        return 'App' . '\\' . config('jsoncrud.paths.generators.models');
    }

    protected function getJsonFileName()
    {
        return studly_case($this->config->name).'.json';
    }
}
