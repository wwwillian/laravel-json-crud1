<?php

namespace Wwwillian\JsonCrud\Traits;

use Wwwillian\JsonCrud\Traits\ModuleCommand;

trait JsonCommand
{
    use ModuleCommand;

    private $config= [];

    protected function configurate($configureModule = true)
    {
        if (config('jsoncrud.hasmodule') && $configureModule) {
            $this->setModule();
        }
        
        $this->setArguments();
        $this->setOptions();
        $this->setArrayOptions();

        return (object) $this->config;
    }

    protected function setArguments() {
        foreach ($this->arguments as $argument) {
            $this->config[$argument] = $this->argument($argument);
        }
        return $this->config;
    }

    protected function setOptions() {
        if(!isset($this->options))
        {
            return $this->config;
        }
        foreach ($this->options as $key => $value) {
            switch(gettype($key)) {
                case 'string' : 
                    $input = $this->option($key);
                    if ($input != "" && !in_array($input, $value)) {
                        $this->error('The value '.$input.' is not a valid option');
                    }
                    $this->config[$key] = $input;
                    break;
                case 'integer' : 
                    $input = $this->option($value);
                    $this->config[$value] = $input;
                    break;
            }
        }
    }

    protected function setArrayOptions() {
        if(!isset($this->arrayOptions) || empty($this->arrayOptions))
        {
            return $this->config;
        }
        foreach ($this->arrayOptions as $key => $value) {
            $userInput = $this->option($key);
            
            if ($userInput == "") {
                continue;
            }

            $inputArray = explode(',', $userInput);

            if ($inputArray[0] == "") {
                continue;
            }
            
            if (count(array_intersect($inputArray, $value)) != count($inputArray)) {
                $this->error('The value '.$userInput.' is not a valid option');
            }

            $this->config[$key] = $userInput;
        }
    }
}
