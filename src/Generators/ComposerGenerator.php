<?php

namespace Wwwillian\JsonCrud\Generators;

use Wwwillian\JsonCrud\Generators\BaseGenerator;
use Wwwillian\JsonCrud\Supports\Stub;

class ComposerGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getTemplateContents()
    {
        return (new Stub('/composer.stub', [
            'studlyModuleName'         => $this->getStudlyModuleName(),
            'lowerModuleName'          => $this->getLowerModuleName(),
            'routeProviderClass'       => str_replace('/', "\\\\", $this->getRouteProviderClass()),
            'moduleNamespace'          => $this->getModuleNamespace(),
            'moduleNamespaceBackslash' => str_replace('/', "\\\\", $this->getModuleNamespace()),
            'authorEmail'              => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'authorName'               => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorUser'               => config('jsoncrud.author.user', 'wwwillian'),
            'authorRole'               => config('jsoncrud.author.role', 'Developer'),
            'projectName'              => config('jsoncrud.project.name', 'Laravel Json Crud')
        ]))->render();
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->name);
        }
        return config('jsoncrud.project.name', 'Project');
    }

    protected function getLowerModuleName()
    {
        return snake_case($this->config->name);
    }

    protected function getRouteProviderClass()
    {
        return $this->getModuleNamespace() . '/Providers/RouteServiceProvider';
    }

    protected function getModuleNamespace()
    {
        return studly_case(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName();
    }

    protected function getDestinationDirectory()
    {
        return config('jsoncrud.paths.modules') . '/' . $this->getStudlyModuleName() . '/';
    }

    protected function getFileName()
    {
        return 'module.json';
    }
}
