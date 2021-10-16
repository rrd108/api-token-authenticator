# CakePHP ApiTokenAuthenticator Plugin

A Simple Token Authentication Plugin for CakePHP 4 REST API-s.

## Configuration

For defaults see `config/apiTokenAuthenticator.php` file in the plugin's directory.

If you want to change any of the values then create your own `apiTokenAuthenticator.php` file at your project's `config` directory. In your config file, you should use only those keys that you want to change. It will be merged to the default one. So, for example, if you are happy with all the options, except in your case the token's header name is `Authorization`, then you have to put this into your on config file.

```php
<?php
return [
  'ApiTokenAuthenticator' => [
    'header' => 'Authorization',
  ]
];
```

## Authentication

The plugin authentication workflow is the following.

At your client appliacation you should send a POST request to `/users/login.json` (or what you set in your `apiTokenAuthenticator.php` file) with a JSON object like this.

```json
{
	"email": "rrd@webmania.cc",
	"password": "rrd"
}
```

If the login was successful than you will get a response like this.

```json
{
	"user": {
		"id": 1,
		"token": "yourSecretTokenComingFromTheDatabase"
	}
}
```

Than you can use this token to authenticate yourself for accessing urls what requires authentication. The token should be sent in a request header named `Token` (or what you set in your `apiTokenAuthenticator.php` file).

## Installation

Including the plugin is pretty much as with every other CakePHP plugin:

```bash
composer require rrd108/api-token-authenticator
```

Then, to load the plugin either run the following command:

```bash
bin/cake plugin load ApiTokenAuthenticator
```

or manually add the following line to your app's `src/Application.php` file's `bootstrap()` function:

```php
$this->addPlugin('ApiTokenAuthenticator');
```

You should comment out `CsrfProtectionMiddleware`.

At your `AppController.php` file's `initialize()` function you should include these components:

```php
public function initialize(): void
{
    parent::initialize();
    $this->loadComponent('RequestHandler');
    $this->loadComponent('Authentication.Authentication');
}
```

Update your `src/Model/Entity/User.php` file adding the following.

```php
use Authentication\PasswordHasher\DefaultPasswordHasher;
protected function _setPassword(string $password)
  {
    $hasher = new DefaultPasswordHasher();
    return $hasher->hash($password);
  }
```

As you probably will use JSON urls, do not forget to add this lien to your `routes.php` file.

```php
$builder->setExtensions(['json']);
```

That's it. It should be up and running.

## Login method

Login method is not added automatically, you should implement it. Here is an example how.

```php
public function login()
{
    $result = $this->Authentication->getResult();
    if ($result->isValid()) {
        $userIdentity = $this->Authentication->getIdentity();
        $user = [
            'id' => $userIdentity->id,
            'token' => $userIdentity->token
        ];
        $this->set(compact('user'));
        $this->viewBuilder()->setOption('serialize', ['user']);
    }
}
```

## Access without authentication

If you want to let the users to access a resource without authentication you should state it in the controller's `beforeFilter()` method.

```php
// For example in UsersController.php
public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);
    $this->Authentication->allowUnauthenticated(['index']);
}
```

This will allow users to access `/users.json` url without authentication.

# Migration

## Migration form version 0.1

Version 0.2 is totally backward compatible with version 0.1

By default, now we use CakePHP's default password hashing instead of `md5` as it was less secure.
Inspite of this your current users will be able to login with their current password, but if you want to use the more secure hasing for new users and keep old users as they are, you have to do the following.

1. Make sure in your database the password field is at least 60 characters long.

2. Update your `src/Model/Entity/User.php` file adding the following. By this whenever and old user with and `md5` hashed password updates his/her password it will be hashed with the default hashing algorythm.

```php
use Authentication\PasswordHasher\DefaultPasswordHasher;
protected function _setPassword(string $password)
  {
    $hasher = new DefaultPasswordHasher();
    return $hasher->hash($password);
  }
```

3. In your `apiTokenAuthenticator.php` file you should define this passwordHasher array.

```php
return [
  'ApiTokenAuthenticator' => [
    // any other custom settings
    // ...
      'passwordHasher' => [
        'className' => 'Authentication.Fallback',
        'hashers' => [
          'Authentication.Default', [
            'className' => 'Authentication.Legacy',
            'hashType' => 'md5',
            'salt' => false
          ],
        ]
    ]
  ]
];
```
