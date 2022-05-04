<?php

namespace ConnectMalves\JsonCrud\Traits;

trait ModuleCommand
{
    protected function setModule() {
        $module = studly_case($this->option('module'));

        $this->config['module'] = $module;
    }
}
