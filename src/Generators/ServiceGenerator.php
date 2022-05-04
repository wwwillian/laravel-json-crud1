<?php

namespace Wwwillian\JsonCrud\Generators;

use Wwwillian\JsonCrud\Generators\BaseGenerator;
use Wwwillian\JsonCrud\Supports\Stub;

class ServiceGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getTemplateContents()
    {
        return (new Stub('/service.stub', [
            'studlyModuleName'      => $this->getStudlyModuleName(),
            'className'             => $this->getClassName(),
            'namespace'             => $this->getNamespace(),
            'repositoryClass'       => $this->getRepositoryClass(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Project')
        ]))->render();
    }

    public function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return base_path(config('jsoncrud.paths.modules')) . '/'. $this->getStudlyModuleName().'/'. config('jsoncrud.paths.generators.services').'/';
        }
        return base_path('app/') .  config('jsoncrud.paths.generators.services') . '/';
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
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. config('jsoncrud.paths.generators.services');
        }

        return 'App\\' . config('jsoncrud.paths.generators.services');
    }

    protected function getRepositoryClass()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() .'\\'. config('jsoncrud.paths.generators.repositories').'\\'.$this->getClassName().'::class';
        }

        return 'App\\' . config('jsoncrud.paths.generators.repositories') . '\\' . $this->getClassName() . '::class';
    }
}
