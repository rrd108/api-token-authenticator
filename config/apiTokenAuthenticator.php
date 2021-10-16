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
        // password hasher 
        'passwordHasher' => 'default',
        // if you already have users with md5 passwords than use this instead of the default
        /*
        'passwordHasher' => [
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
