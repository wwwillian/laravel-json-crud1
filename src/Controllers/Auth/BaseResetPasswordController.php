<?php

namespace Wwwillian\JsonCrud\Controllers\Auth;

use Wwwillian\JsonCrud\Controllers\BaseWebController as Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class BaseResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard($this->guard);
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

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view(config('jsoncrud.view', 'jcrud') . '::auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
