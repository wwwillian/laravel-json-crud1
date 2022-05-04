<?php

namespace Wwwillian\JsonCrud\Generators;

use Wwwillian\JsonCrud\Generators\BaseGenerator;
use Wwwillian\JsonCrud\Supports\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;

class AuthGenerator extends BaseGenerator
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getTemplateContents()
    {
        $contents = [];

        $contents[] = (new Stub('/auth/dashboard_controller.stub', [
            'studlyModuleName'      => $this->getStudlyModuleName(),
            'className'             => $this->getDashboardControllerClassName(),
            'namespace'             => $this->getWebNamespace(),
            'authMiddleware'        => 'auth' . $this->getAuthMiddleware(),
            'routePrefix'           => $this->getRouteName() . '.dashboard',
            'serviceClass'          => $this->getServiceClass(),
            'authorName'            => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'           => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'           => config('jsoncrud.project.name', 'Laravel Json Crud'),
            'view'                  => config('jsoncrud.defaults.views.dashboard', 'layouts.dashboard')
        ]))->render();
        $contents[] = (new Stub('/auth/forgot_password.stub', [
            'studlyModuleName' => $this->getStudlyModuleName(),
            'className'        => $this->getForgotPasswordClassName(),
            'namespace'        => $this->getAuthNamespace(),
            'authMiddleware'   => 'guest' . $this->getAuthMiddleware(),
            'serviceClass'     => $this->getServiceClass(),
            'authorName'       => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'      => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'      => config('jsoncrud.project.name', 'Laravel Json Crud'),
        ]))->render();
        $contents[] = (new Stub('/auth/login_controller.stub', [
            'studlyModuleName' => $this->getStudlyModuleName(),
            'className'        => $this->getLoginClassName(),
            'namespace'        => $this->getAuthNamespace(),
            'serviceClass'     => $this->getServiceClass(),
            'authMiddleware'   => 'guest' . $this->getAuthMiddleware(),
            'routeName'        => $this->getRouteName(),
            'guard'            => $this->getGuard(),
            'authorName'       => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'      => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'      => config('jsoncrud.project.name', 'Laravel Json Crud'),
        ]))->render();
        $contents[] = (new Stub('/auth/register_controller.stub', [
            'studlyModuleName' => $this->getStudlyModuleName(),
            'className'        => $this->getRegisterClassName(),
            'namespace'        => $this->getAuthNamespace(),
            'serviceClass'     => $this->getServiceClass(),
            'authMiddleware'   => 'guest' . $this->getAuthMiddleware(),
            'routeName'        => $this->getRouteName(),
            'guard'            => $this->getGuard(),
            'authorName'       => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'      => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'      => config('jsoncrud.project.name', 'Laravel Json Crud'),
        ]))->render();
        $contents[] = (new Stub('/auth/reset_password.stub', [
            'studlyModuleName' => $this->getStudlyModuleName(),
            'className'        => $this->getResetPasswordClassName(),
            'namespace'        => $this->getAuthNamespace(),
            'authMiddleware'   => 'guest' . $this->getAuthMiddleware(),
            'routeName'        => $this->getRouteName(),
            'guard'            => $this->getGuard(),
            'serviceClass'     => $this->getServiceClass(),
            'authorName'       => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'      => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'      => config('jsoncrud.project.name', 'Laravel Json Crud'),
        ]))->render();
        $contents[] = (new Stub('/auth/verify_email.stub', [
            'studlyModuleName' => $this->getStudlyModuleName(),
            'className'        => $this->getVerifyEmailClassName(),
            'namespace'        => $this->getAuthNamespace(),
            'authMiddleware'   => 'auth' . $this->getAuthMiddleware(),
            'routeName'        => $this->getRouteName(),
            'serviceClass'     => $this->getServiceClass(),
            'authorName'       => config('jsoncrud.author.name', 'Matheus Alves'),
            'authorEmail'      => config('jsoncrud.author.email', 'connect.malves@gmail.com'),
            'projectName'      => config('jsoncrud.project.name', 'Laravel Json Crud'),
        ]))->render();
        $newRoute = (new Stub('/route.stub', [
            'controllerName' => $this->getDashboardControllerClassName() . "@home",
            'routeName'      => '/{home?}',
            'routes'         => '',
            'name'           => "->name('dashboard')",
            'type'           => 'get',
        ]))->render();
        $filePaths   = $this->getDestinationDirectory();
        $fileNames   = $this->getFileName();
        $fileContent = file_get_contents(end($filePaths) . end($fileNames));
        $contents[]  = $fileContent . "\n" . $newRoute;

        return $contents;
    }

    public function getDestinationDirectory()
    {
        $paths = [];
        if (config('jsoncrud.hasmodule', false)) {
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.webcontrollers') . '/';
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.authcontrollers') . '/';
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.authcontrollers') . '/';
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.authcontrollers') . '/';
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.authcontrollers') . '/';
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.paths.generators.authcontrollers') . '/';
            $paths[] = base_path(config('jsoncrud.paths.modules')) . '/' . $this->getStudlyModuleName() . '/' . config('jsoncrud.files.webroutes');
        } else {
            $paths[] = base_path('app/') . config('jsoncrud.paths.generators.webcontrollers') . '/'; 
            $paths[] = base_path('app/') . config('jsoncrud.paths.generators.authcontrollers') . '/'; 
            $paths[] = base_path('app/') . config('jsoncrud.paths.generators.authcontrollers') . '/'; 
            $paths[] = base_path('app/') . config('jsoncrud.paths.generators.authcontrollers') . '/'; 
            $paths[] = base_path('app/') . config('jsoncrud.paths.generators.authcontrollers') . '/'; 
            $paths[] = base_path('app/') . config('jsoncrud.paths.generators.authcontrollers') . '/'; 
            $paths[] =  '';
        }
        return $paths;
    }

    public function getFileName()
    {
        $filenames = [];

        $filenames[] = $this->getDashboardControllerClassName() . '.php';
        $filenames[] = $this->getForgotPasswordClassName() . '.php';
        $filenames[] = $this->getLoginClassName() . '.php';
        $filenames[] = $this->getRegisterClassName() . '.php';
        $filenames[] = $this->getResetPasswordClassName() . '.php';
        $filenames[] = $this->getVerifyEmailClassName() . '.php';
        $filenames[] = config('jsoncrud.files.webroutes');

        return $filenames;
    }

    public function generate()
    {
        $this->laravel  = App::getFacadeApplication();

        $directories    = $this->getDestinationDirectory();
        $filenames      = $this->getFileName();
        $contents       = $this->getTemplateContents();
        $filesystem     = new Filesystem();

        foreach ($directories as $index => $directory) {
            if (!$filesystem->isDirectory($directory) && $directory != "") {
                $filesystem->makeDirectory($directory, 0755, true);
            }
            
            $filesystem->put($directory . $filenames[$index], $contents[$index]);
        }
    }

    protected function getStudlyModuleName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->module);
        }
        
        return config('jsoncrud.project.name', 'Project');
    }

    protected function getDashboardControllerClassName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return studly_case($this->config->module) . 'DashboardController';
        }

        return 'DashboardController';
    }

    protected function getVerifyEmailClassName()
    {
        return 'VerifyEmailController';
    }

    protected function getResetPasswordClassName()
    {
        return 'ResetPasswordController';
    }

    protected function getRegisterClassName()
    {
        return 'RegisterController';
    }

    protected function getLoginClassName()
    {
        return 'LoginController';
    }

    protected function getForgotPasswordClassName()
    {
        return 'ForgotPasswordController';
    }

    protected function getWebNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return config('jsoncrud.paths.modules') . '\\' . $this->getStudlyModuleName() . '\\' . config('jsoncrud.paths.generators.webcontrollers');
        }
        return 'App\\' . config('jsoncrud.paths.generators.webcontrollers');
    }

    protected function getAuthNamespace()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return config('jsoncrud.paths.modules') . '\\' . $this->getStudlyModuleName() . '\\' . config('jsoncrud.paths.generators.authcontrollers');
        }
        return 'App\\' . config('jsoncrud.paths.generators.authcontrollers');

    }

    protected function getServiceClass()
    { 
        if (config('jsoncrud.hasmodule', false)) {
            return config('jsoncrud.paths.modules') . '\\' . $this->getStudlyModuleName() . '\\' . config('jsoncrud.paths.generators.services') . '\\' . $this->config->module . "::class";
        }
        return 'App\\' . config('jsoncrud.paths.generators.services') . '\\' . $this->config->model . "::class";

    }

    protected function getAuthMiddleware()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return $this->getRouteName();
        }

        return '';
    }

    protected function getRouteName()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return snake_case(str_plural($this->config->module));
        }

        return snake_case(str_plural($this->config->model));
    }

    protected function getGuard()
    {
        if (config('jsoncrud.hasmodule', false)) {
            return snake_case(str_plural($this->config->module));
        }

        return config('auth.defaults.guard');
    }
}
