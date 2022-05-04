<?php

if (! function_exists('dot_to_square_brackets')) {
    function dot_to_square_brackets($string)
    {
        $string = preg_replace('/\*/', '', $string);
        $relations = explode('.', $string);
        $model = array_shift($relations);
        $string = $model;
        if(count($relations) > 0) {
            $string .= '[' . implode('][', $relations) . ']';
        }
        return $string;
    }
}

if (! function_exists('auth_routes')) {
    function auth_routes(array $options = [])
    {
        // Authentication Routes...
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('login', 'Auth\LoginController@login');
        Route::post('logout', 'Auth\LoginController@logout')->name('logout');

        // Registration Routes...
        if ($options['register'] ?? true) {
            Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
            Route::post('register', 'Auth\RegisterController@register');
        }

        // Password Reset Routes...
        Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

        // Email Verification Routes...
        if ($options['verify'] ?? false) {
            Route::get('email/verify', 'Auth\VerifyEmailController@show')->name('verification.notice');
            Route::get('email/verify/{id}', 'Auth\VerifyEmailController@verify')->name('verification.verify');
            Route::get('email/resend', 'Auth\VerifyEmailController@resend')->name('verification.resend');
        }
    }
}

if (! function_exists('is_many_array')) {
    function is_many_array($array)
    {
        if(!is_array($array) || count($array) < 1) {
            return false;
        }
        foreach($array as $key => $item) {
            if(!is_int($key)){
                return false;
            }
        }
        return true;
    }
}

if (! function_exists('str_lower')) {
    function str_lower($word)
    {
        return mb_strtolower($word);
    }
}
