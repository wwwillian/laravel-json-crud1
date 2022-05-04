<?php

namespace ConnectMalves\JsonCrud\Generators;

use ConnectMalves\JsonCrud\Generators\BaseGenerator;
use ConnectMalves\JsonCrud\Supports\Stub;

class RepositoryGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getTemplateContents()
    {
        return (new Stub('/repository.stub', [
            'studlyModuleName'      => $this->getStudlyModuleName(),
            'className'             => $this->getClassName(),
            'namespace'             => $this->getNamespace(),
            'modelClass'            => $this->getModelClass(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Project')
        ]))->render();
    }

    public function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName().'/'. config('jsoncrud.paths.generators.repositories'). '/';
        }

        return base_path('app/') . config('jsoncrud.paths.generators.repositories') . '/';
    }

    public function getFileName()
    {
        return $this->getClassName() . '.php';
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
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. config('jsoncrud.paths.generators.repositories');
        }

        return 'App\\' . config('jsoncrud.paths.generators.repositories');
    }

    protected function getModelClass()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. config('jsoncrud.paths.generators.models').'\\'.$this->getClassName().'::class';
        }

        return 'App\\' . config('jsoncrud.paths.generators.models') . '\\' . $this->getClassName() . '::class';
    }
}
