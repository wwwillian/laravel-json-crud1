<?php

namespace ConnectMalves\JsonCrud\Commands;

use Illuminate\Console\Command;
use ConnectMalves\JsonCrud\Traits\JsonCommand;
use ConnectMalves\JsonCrud\Generators\ServiceGenerator;

class JsonMakeService extends Command
{
    use JsonCommand;

    protected $arguments = [
        'name'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:service {name : The service name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json service';

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

        $serviceGenerator = new ServiceGenerator($config);
        $serviceGenerator->generate();

        $this->info('Service generated successfully');
    }
}
