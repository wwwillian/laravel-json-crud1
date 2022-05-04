<?php

namespace Wwwillian\JsonCrud\Controllers\Auth;

use Wwwillian\JsonCrud\Controllers\BaseWebController as Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BaseRegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        event(new Registered($user = $this->creat($request)));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ? : redirect($this->redirectPath());
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view(config('jsoncrud.view', 'jcrud') . '::auth.register');
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * The route name where the user will be redirected after login.
     *
     * @var string
     */
    protected $redirectToRouteName = 'home';

    /*
     * The middleware of the controller
     */
    protected $authMiddleware = 'guest';

    /*
     * The guard of the controller
     */
    protected $guard = 'web';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware($this->authMiddleware);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Illuminate\Contracts\Auth\Authenticatable
     */
    protected function creat(Request $request)
    {      
        return $this->service->create($request);
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        if (property_exists($this, 'redirectToRouteName')) {
            return route($this->redirectToRouteName);
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '\home';
    }
}
