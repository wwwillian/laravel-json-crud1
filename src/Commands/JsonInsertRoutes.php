<?php

namespace ConnectMalves\JsonCrud\Commands;

use ConnectMalves\JsonCrud\Traits\JsonCommand;
use Illuminate\Console\Command;
use ConnectMalves\JsonCrud\Generators\InsertApiRouteGenerator;
use ConnectMalves\JsonCrud\Generators\InsertWebRouteGenerator;

class JsonInsertRoutes extends Command
{
    use JsonCommand;

    protected $arguments = [
        'route',
        'controller'
    ];

    protected $arrayOptions = [
        'except' => [
            'create',
            'edit',
            'destroy',
            'index',
            'update',
            'store',
            'show'
        ],
        'only' => [
            'create',
            'edit',
            'destroy',
            'index',
            'update',
            'store',
            'show'
        ]
    ];

    protected $options = [
        'authenticatable',
        'type' => [
            'web',
            'api',
            'all'
        ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:insert:routes {route : The route name}
                                               {controller : The controller name}
                                               {--T|type=all : Create controller types; Options: web, api or all}
                                               {--O|only= : Specify the route methods that will be created; Options: create, edit, destroy, index, update, store, show}
                                               {--E|except= : Specify the route methods that will not be created; Options: create, edit, destroy, index, update, store, show}
                                               {--A|authenticatable : Create an authenticatable route}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud json configuratable';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('jsoncrud.hasmodule')) {
            $this->signature .= "{--M|module=Core : The module name}";
        }
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = $this->configurate();
        
        switch ($config->type) {
            case 'all':
                $webroute = new InsertWebRouteGenerator($config);
                $apiroute = new InsertApiRouteGenerator($config);
                $webroute->generate();
                $apiroute->generate();
                break;
            case 'web':
                $webroute = new InsertWebRouteGenerator($config);
                $webroute->generate();
                break;
            case 'api':
                $apiroute = new InsertApiRouteGenerator($config);
                $apiroute->generate();
                break;
        }

        $this->info('Routes generated successfully');
    }
}
