<?php

return [

    'project'   => [
        'name' => 'Laravel Json Crud',
    ],

    'author'    => [
        'name'  => 'Matheus Alves',
        'email' => 'connect.malves@gmail.com',
    ],

    'files'     => [
        'webroutes' => 'routes/web.php',
        'apiroutes' => 'routes/api.php',
    ],

    'hasmodule' => false,

    'paths'     => [
        'modules'    => 'modules',
        'stubs'      => 'vendor/wwwillian/laravel-json-crud/src/generators/stubs',
        'generators' => [
            'authcontrollers' => 'Controllers/Web/Auth',
            'webcontrollers'  => 'Controllers/Web',
            'apicontrollers'  => 'Controllers/Api',
            'commands'        => 'Console/Commands',
            'middleware'      => 'Middleware',
            'models'          => 'Models',
            'providers'       => 'Providers',
            'exceptions'      => 'Exceptions',
            'services'        => 'Services',
            'repositories'    => 'Repositories',
            'assets'          => 'resources/assets',
            'js'              => 'resources/assets/js',
            'scss'            => 'resources/assets/scss',
            'json'            => 'resources/json',
            'lang'            => 'resources/lang',
        ],
    ],

    'view' => 'jcrud'
];
