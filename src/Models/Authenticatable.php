<?php

namespace ConnectMalves\JsonCrud\Models;

use ConnectMalves\JsonCrud\Models\BaseModel;
use ConnectMalves\JsonCrud\Traits\CustomAuthentication;
use ConnectMalves\JsonCrud\Traits\CustomCanResetPassword;
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
