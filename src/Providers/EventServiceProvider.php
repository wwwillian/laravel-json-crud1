<?php

namespace Wwwillian\JsonCrud\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Wwwillian\JsonCrud\Events\BaseModelSaving as Event;
use Wwwillian\JsonCrud\Listeners\BaseModelSaving as Listener;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Event::class => [
            Listener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
    
}
