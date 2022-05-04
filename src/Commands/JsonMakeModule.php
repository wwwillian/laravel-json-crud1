<?php

namespace ConnectMalves\JsonCrud\Commands;

use ConnectMalves\JsonCrud\Generators\ComposerGenerator;
use ConnectMalves\JsonCrud\Generators\RouteProviderGenerator;
use ConnectMalves\JsonCrud\Traits\JsonCommand;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class JsonMakeModule extends Command
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
    protected $signature = 'json:make:module {name : The module name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->config = $this->configurate($configureModule = false);

        $this->makeModule($this->config->name);
        $this->makeRouteProvider();
        $this->makeRoutes();
        $this->makeComposer();
        shell_exec('composer dump-autoload');
    }

    protected function makeComposer()
    {
        $composerGenerator = new ComposerGenerator($this->config);
        $composerGenerator->generate();
    }

    protected function makeModule($name)
    {
        $names[] = str_lower($name);

        $this->call('module:make', [
            'name' => $names,
            '--plain' => true
        ]);
    }

    protected function makeRouteProvider()
    {
        $routesProvider = new RouteProviderGenerator($this->config);
        $routesProvider->generate();
    }

    protected function makeRoutes()
    {
        
        $this->makeWebRoutes();
        $this->makeApiRoutes();
    }

    protected function makeWebRoutes()
    {
        $path = config('jsoncrud.paths.modules').'/'.$this->config->name.'/'.config('jsoncrud.files.webroutes');
        $contents = file_get_contents(config('jsoncrud.paths.stubs').'/routes.stub');
        
        $this->filesystem->put($path, $contents);
    }

    protected function makeApiRoutes()
    {
        $path = config('jsoncrud.paths.modules').'/'.$this->config->name.'/'.config('jsoncrud.files.apiroutes');
        $contents = file_get_contents(config('jsoncrud.paths.stubs').'/api_routes.stub');
        
        $this->filesystem->put($path, $contents);
    }
}
