<?php

namespace Wwwillian\JsonCrud\Commands;

use Illuminate\Console\Command;
use Wwwillian\JsonCrud\Generators\RouteProviderGenerator;

class JsonRouteProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:route-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Route provider';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeProviderGenerator = new RouteProviderGenerator();
        $routeProviderGenerator->generate();

        $this->info('Route Provider generated successfully');
    }
}
