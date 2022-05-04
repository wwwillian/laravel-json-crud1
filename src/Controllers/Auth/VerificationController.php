<?php

namespace Wwwillian\JsonCrud\Controllers\Auth;

use Wwwillian\JsonCrud\Controllers\BaseWebController as Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be resent if the user did not receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
    protected $authMiddleware = 'auth';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware($this->authMiddleware);
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
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
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view(config('jsoncrud.view', 'jcrud') . '::auth.verify');
    }
}
