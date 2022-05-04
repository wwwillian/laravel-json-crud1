<?php

namespace ConnectMalves\JsonCrud\Controllers\Auth;

use ConnectMalves\JsonCrud\Controllers\BaseWebController as Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class BaseForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /*
     * The middleware of the controller
     */
    protected $authMiddleware = 'guest';

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
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view(config('jsoncrud.view', 'jcrud') . '::auth.passwords.email');
    }
}
