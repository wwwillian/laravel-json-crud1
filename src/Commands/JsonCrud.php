<?php

namespace ConnectMalves\JsonCrud\Commands;

use ConnectMalves\JsonCrud\Traits\JsonCommand;
use Illuminate\Console\Command;
use ConnectMalves\JsonCrud\Generators\JsonCrudGenerator;

class JsonCrud extends Command
{
    use JsonCommand;

    protected $arguments = [
        'name',
    ];

    protected $options = [
        'authenticatable',
        'json',
        'except',
        'only',
        'routes'=>[
            'web',
            'api',
            'all'
        ],
        'controllers' => [
            'web',
            'api',
            'all'
        ]
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

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:crud {name : The crud name}
                                      {--A|authenticatable : Create an authenticatable model}
                                      {--R|routes=all : Auto generate route types}
                                      {--J|json : Create an empty json configuration file}
                                      {--C|controllers=all : Create controller types; Options: web, api or all}
                                      {--O|only= : Specify the route methods that will be created; Options: create, edit, destroy, index, update, store, show}
                                      {--E|except= : Specify the route methods that will not be created; Options: create, edit, destroy, index, update, store, show}';

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
        
        $crudGenerator = new JsonCrudGenerator($this, $config);
        $crudGenerator->generate();
    }
}
