<?php

namespace ConnectMalves\JsonCrud\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReflectionClass;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The service.
     */
    protected $service;

    /**
     * The routePrefix.
     */
    protected $routePrefix;

    public function entity()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function routePrefix()
    {
        return isset($this->routePrefix) ? $this->routePrefix : str_plural((snake_case($this->entity())));
    }

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->service = app()->make($this->serviceClass);
        if(isset($this->authMiddleware)) {
            isset($this->authException) ? 
                $this->middleware($this->authMiddleware)->except($this->authException) : 
                $this->middleware($this->authMiddleware);
        }
    }
}
