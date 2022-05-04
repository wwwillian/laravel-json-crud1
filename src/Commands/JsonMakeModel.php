<?php

namespace ConnectMalves\JsonCrud\Commands;

use Illuminate\Console\Command;
use ConnectMalves\JsonCrud\Traits\JsonCommand;
use ConnectMalves\JsonCrud\Generators\ModelGenerator;

class JsonMakeModel extends Command
{
    use JsonCommand;

    protected $arguments = [
        'name',
    ];

    protected $options = [
        'authenticatable'
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:model {name : The model name}
                                            {--A|authenticatable : Create an authenticatable model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json model';

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

        $modelGenerator = new ModelGenerator($config);
        $modelGenerator->generate();

        $this->info('Model generated successfully');
    }
}
