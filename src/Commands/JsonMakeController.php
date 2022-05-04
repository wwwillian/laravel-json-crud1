<?php

namespace ConnectMalves\JsonCrud\Commands;

use Illuminate\Console\Command;
use ConnectMalves\JsonCrud\Traits\JsonCommand;
use ConnectMalves\JsonCrud\Generators\ApiControllerGenerator;
use ConnectMalves\JsonCrud\Generators\WebControllerGenerator;

class JsonMakeController extends Command
{
    use JsonCommand;

    protected $arguments = [
        'name',
    ];

    protected $options = [
        'controllers' => [
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
    protected $signature = 'json:make:controller {name : The api controller name}
                                                 {--C|controllers= : Create controller types; Options: web, api or all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json controller';

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
     */
    public function handle()
    {
        $config = $this->configurate();
        switch ($config->controllers) {
            case 'all':
                $webcontroller = new ApiControllerGenerator($config);
                $apicontroller = new WebControllerGenerator($config);
                $webcontroller->generate();
                $apicontroller->generate();
                break;
            case 'web':
                $webcontroller = new WebControllerGenerator($config);
                $webcontroller->generate();
                break;
            case 'api':
                $apicontroller = new ApiControllerGenerator($config);
                $apicontroller->generate();
                break;
        }
        
        $this->info('Controllers generated successfully');
    }
}
