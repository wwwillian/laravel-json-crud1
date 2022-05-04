<?php

namespace ConnectMalves\JsonCrud\Generators;

use Illuminate\Support\Facades\App;
use Illuminate\Filesystem\Filesystem;

abstract class BaseGenerator
{
    abstract protected function getDestinationDirectory();
    abstract protected function getFileName();
    abstract protected function getTemplateContents();
    
    protected $laravel;

    public function generate()
    {
        $this->laravel = App::getFacadeApplication();

        $directory = $this->getDestinationDirectory();
        $filename = $this->getFileName();
        $contents = $this->getTemplateContents();
        
        $filesystem = new Filesystem();

        if(!$filesystem->isDirectory($directory) && $directory != "") {
            $filesystem->makeDirectory($directory, 0755, true);
        }

        $filesystem->put($directory . $filename, $contents);
    }
}
