<?php

namespace Wwwillian\JsonCrud\Commands;

use Illuminate\Console\Application as Artisan;
use Illuminate\Support\ServiceProvider;

use Wwwillian\JsonCrud\Commands\JsonCrud;
use Wwwillian\JsonCrud\Commands\JsonMakeModule;
use Wwwillian\JsonCrud\Commands\JsonInsertRoutes;
use Wwwillian\JsonCrud\Commands\JsonMakeController;
use Wwwillian\JsonCrud\Commands\JsonMakeJsonFile;
use Wwwillian\JsonCrud\Commands\JsonMakeModel;
use Wwwillian\JsonCrud\Commands\JsonMakeRepository;
use Wwwillian\JsonCrud\Commands\JsonMakeService;
use Wwwillian\JsonCrud\Commands\JsonAuth;
use Wwwillian\JsonCrud\Commands\JsonRouteProvider;
use Wwwillian\JsonCrud\Generators\RouteProviderGenerator;


class CommandsServiceProvider extends ServiceProvider
{
    private $commands = [
        JsonCrud::class,
        JsonInsertRoutes::class,
        JsonMakeController::class,
        JsonMakeJsonFile::class,
        JsonMakeModel::class,
        JsonMakeRepository::class,
        JsonMakeService::class,
        JsonAuth::class
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (config('jsoncrud.hasmodule', false)) {
            $this->commands[] = JsonMakeModule::class;
        } else {
            $this->commands[] = JsonRouteProvider::class;
        }

        $this->commands($this->commands);
    }

    /**
     * boot services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('jsoncrud.php'),
        ]);

        if(config('jsoncrud.hasmodule', false))
        {
            $this->publishes([
                __DIR__ . '/../modules.php' => config_path('modules.php'),
            ]);
        }
    }

    /**
     * Register the package's custom Artisan commands.
     *
     * @param  array|mixed  $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
        });
    }
}
