<?php

namespace ConnectMalves\JsonCrud\Generators;

use ConnectMalves\JsonCrud\Generators\ApiControllerGenerator;
use ConnectMalves\JsonCrud\Generators\WebControllerGenerator;

class JsonCrudGenerator
{
    protected $configurations;
    protected $console;

    public function __construct($console, $configurations)
    {
        $this->console        = $console;
        $this->configurations = $configurations;
    }

    public function generate()
    {
        $this->generateModel();
        $this->generateRoutes();
        $this->generateService();
        $this->generateController();
        $this->generateRepository();
        $this->generateJsonFile();
    }

    protected function generateModel()
    {
        $config = $this->configurations;

        if (config('jsoncrud.hasmodule')) {
            $this->console->call(
                'json:make:model',
                [
                    'name'              => snake_case($config->name),
                    '--module'          => $config->module,
                    '--authenticatable' => $config->authenticatable,
                ]
            );
        } else {
            $this->console->call(
                'json:make:model',
                [
                    'name'              => snake_case($config->name),
                    '--authenticatable' => $config->authenticatable,
                ]
            );
        }
    }

    protected function generateRoutes()
    {
        $config = $this->configurations;
        if ($config->routes) {
            if (config('jsoncrud.hasmodule')) {
                $this->console->call(
                    'json:insert:routes',
                    [
                        'route'      => snake_case($config->name),
                        'controller' => studly_case($config->name),
                        '--module'   => $config->module,
                        '--type'     => $config->routes,
                        '--except'   => $config->except,
                        '--only'     => $config->only,
                    ]
                );
            } else {
                $this->console->call(
                    'json:insert:routes',
                    [
                        'route'      => snake_case($config->name),
                        'controller' => studly_case($config->name),
                        '--type'     => $config->routes,
                        '--except'   => $config->except,
                        '--only'     => $config->only,
                    ]
                );
            }
        } else {
            $this->console->info('Routes skipped');
        }
    }

    protected function generateService()
    {
        $config = $this->configurations;
        if (config('jsoncrud.hasmodule')) {
            $this->console->call(
                'json:make:service',
                [
                    'name'     => snake_case($config->name),
                    '--module' => $config->module,
                ]
            );
        } else {
            $this->console->call(
                'json:make:service',
                [
                    'name'     => snake_case($config->name)
                ]
            );
        }
    }

    protected function generateController()
    {
        $config = $this->configurations;

        switch ($config->controllers) {
            case 'web':
                $controllerGenerator = new WebControllerGenerator($config);
                $controllerGenerator->generate();
                $this->console->info('Web Controller generated successfully');
                break;
            case 'api':
                $controllerGenerator = new ApiControllerGenerator($config);
                $controllerGenerator->generate();
                $this->console->info('Api Controller generated successfully');
                break;
            case 'all':
            default:
                $webControllerGenerator = new WebControllerGenerator($config);
                $apiControllerGenerator = new ApiControllerGenerator($config);
                $webControllerGenerator->generate();
                $apiControllerGenerator->generate();
                $this->console->info('Api Controller generated successfully');
                $this->console->info('Web Controller generated successfully');
                break;
        }
    }

    protected function generateRepository()
    {
        $config = $this->configurations;

        if (config('jsoncrud.hasmodule')) {
            $this->console->call(
                'json:make:repository',
                [
                    'name'     => studly_case($config->name),
                    '--module' => $config->module,
                ]
            );
        } else {
            $this->console->call(
                'json:make:repository',
                [
                    'name'     => studly_case($config->name),
                ]
            );
        }
    }

    protected function generateJsonFile()
    {
        $config = $this->configurations;

        if ($config->json) {
            if (config('jsoncrud.hasmodule')) {
                $this->console->call(
                    'json:make:jsonfile',
                    [
                        'name'     => studly_case($config->name),
                        '--module' => $config->module,
                    ]
                );
            } else {
                $this->console->call(
                    'json:make:jsonfile',
                    [
                        'name'     => studly_case($config->name),
                    ]
                );
            }
        } else {
            $this->console->info('Json File generation skipped');
        }
    }
}
