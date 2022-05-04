<?php

namespace Wwwillian\JsonCrud\Commands;

use Illuminate\Console\Command;
use Wwwillian\JsonCrud\Traits\JsonCommand;
use Wwwillian\JsonCrud\Generators\RepositoryGenerator;

class JsonMakeRepository extends Command
{
    use JsonCommand;

    protected $arguments = [
        'name',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:repository {name : The repository name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json repository';

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

        $repositoryGenerator = new RepositoryGenerator($config);
        $repositoryGenerator->generate();
        
        $this->info('Repository generated successfully');
    }
}
