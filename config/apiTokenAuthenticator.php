<?php

return [
    'ApiTokenAuthenticator' => [
        // database fileds to identify the user
        'fields' => [
            'username' => 'email',
            'password' => 'password'
        ],
        // name of the header for the token
        'header' => 'Token',
        // login controller and action
        'login' => [
            'controller' => 'Users',
            'action' => 'login.json'

        ],

        // if you want to use a different password hasher than the default you can define it here
        /*'passwordHasher' => [
            'className' => 'Authentication.Fallback',
            'hashers' => [
                'Authentication.Default',
                [
                    'className' => 'Authentication.Legacy',
                    'hashType' => 'md5',
                    'salt' => false
                ],
            ]
        ]*/

    ]
];
