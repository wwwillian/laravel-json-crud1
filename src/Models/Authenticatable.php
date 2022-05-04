<?php

namespace Wwwillian\JsonCrud\Models;

use Wwwillian\JsonCrud\Models\BaseModel;
use Wwwillian\JsonCrud\Traits\CustomAuthentication;
use Wwwillian\JsonCrud\Traits\CustomCanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

class Authenticatable extends BaseModel implements
AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract
{
    use CustomAuthentication, Authorizable, CustomCanResetPassword;
}
