<?php

namespace ConnectMalves\JsonCrud\Generators;

use ConnectMalves\JsonCrud\Generators\BaseGenerator;
use ConnectMalves\JsonCrud\Supports\Stub;

class RouteProviderGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config = null)
    {
        $this->config = $config;
    }

    protected function getTemplateContents()
    {
        return (new Stub('/route_provider.stub', [
            'namespace'             => $this->getNamespace(),
            'studlyModuleName'      => $this->getStudlyModuleName(),
            'controllersNamespace'  => $this->getControllersNamespace(),
            'webRoute'              => $this->getWebRoute(),
            'apiRoute'              => $this->getApiRoute(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Project')
        ]))->render();
    }

    protected function getRouteName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return snake_case(str_plural($this->config->name));
        }
        return '';
    }

    protected function getModuleNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return config('jsoncrud.paths.modules').'/'.$this->getStudlyModuleName();
        }

        return 'App\\Http\\Controllers';
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->name);
        }

        return config('jsoncrud.project.name', 'Project');
    }

    protected function getNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() . '\\' . config('jsoncrud.paths.generators.providers');
        }

        return 'App\\' . config('jsoncrud.paths.generators.providers');
    }

    protected function getControllersNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case(config('jsoncrud.paths.modules')) .'\\'. $this->getStudlyModuleName() . '\\Controllers';
        }

        return 'App\\Http\\Controllers' ;
    }

    protected function getWebRoutesPath()
    {
        return config('jsoncrud.stubs.files.webroutes');
    }

    protected function getApiRoutesPath()
    {
        return config('jsoncrud.stubs.files.apiroutes');
    }

    protected function getFileName()
    {
        return 'RouteServiceProvider.php';
    }

    protected function getDestinationDirectory()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return base_path(config('jsoncrud.paths.modules')) .'/'.$this->getStudlyModuleName().'/'.config('jsoncrud.paths.generators.providers') . '/';
        }

        return  base_path('app/') . config('jsoncrud.paths.generators.providers') . '/';
    }

    protected function getWebRoute()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return "Route::prefix('" . $this->getRouteName() . "/')
                    ->name('" . $this->getRouteName() . ".')
                    ->middleware('web')
                    ->namespace(\$this->namespace . '\Web')
                    ->group(base_path('" . $this->getModuleNamespace() . "/routes/web.php'));";
        }

        return "Route::middleware('web')
                    ->namespace(\$this->namespace . '\Web')
                    ->group(base_path('routes/web.php'));";
    }

    protected function getApiRoute()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return "Route::prefix('api/v' . \$version . '/" . $this->getRouteName(). "/')
                    ->name('" . $this->getRouteName() . ".')
                    ->middleware('api')
                    ->namespace(\$this->namespace . '\Api')
                    ->group(base_path('" . str_lower($this->getModuleNamespace()) . "/routes/' . \$routeFile));";
        }

        return "Route::prefix('api/v' . \$version )
                    ->middleware('api')
                    ->namespace(\$this->namespace . '\Api')
                    ->group(base_path('routes/' . \$routeFile));";
    }
}
