<?php

namespace ConnectMalves\JsonCrud\Commands;

use Illuminate\Console\Application as Artisan;
use Illuminate\Support\ServiceProvider;

use ConnectMalves\JsonCrud\Commands\JsonCrud;
use ConnectMalves\JsonCrud\Commands\JsonMakeModule;
use ConnectMalves\JsonCrud\Commands\JsonInsertRoutes;
use ConnectMalves\JsonCrud\Commands\JsonMakeController;
use ConnectMalves\JsonCrud\Commands\JsonMakeJsonFile;
use ConnectMalves\JsonCrud\Commands\JsonMakeModel;
use ConnectMalves\JsonCrud\Commands\JsonMakeRepository;
use ConnectMalves\JsonCrud\Commands\JsonMakeService;
use ConnectMalves\JsonCrud\Commands\JsonAuth;
use ConnectMalves\JsonCrud\Commands\JsonRouteProvider;
use ConnectMalves\JsonCrud\Generators\RouteProviderGenerator;


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
