<?php

namespace ConnectMalves\JsonCrud\Traits;

use Illuminate\Support\ServiceProvider;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\JsonValidator;

class JsonParserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance(JsonDecoder::class, new JsonDecoder);
        $this->app->instance(JsonValidator::class, new JsonValidator);
    }
}
