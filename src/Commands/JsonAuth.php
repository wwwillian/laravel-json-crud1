<?php

namespace Wwwillian\JsonCrud\Commands;

use Illuminate\Console\Command;
use Wwwillian\JsonCrud\Traits\JsonCommand;
use Wwwillian\JsonCrud\Generators\AuthGenerator;

class JsonAuth extends Command
{
    use JsonCommand;

    protected $arguments = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:auth ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new auth controller';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('jsoncrud.hasmodule')) {
            $this->signature  .= "{--M|module=Core : The module name}";
        } else {
            $this->signature  .= "{model : The model name}";
            $this->arguments[] = "model";
        }
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->configurate();

        $name = '';

        if (config('jsoncrud.hasmodule')) {
            $this->call('json:make:crud', [
                'name'              =>  $config->module,
                '--module'          =>  $config->module,
                '--authenticatable' => true
            ]);
        } else {
            $this->call('json:make:crud', [
                'name'              =>  $config->model,
                '--authenticatable' => true
            ]);
        }

        $authcontrollers = new AuthGenerator($config);
        $authcontrollers->generate();
        
        $this->info('Auth controllers generated successfully');
    }
}
