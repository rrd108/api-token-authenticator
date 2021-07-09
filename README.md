# CakePHP ApiTokenAuthenticator Plugin

A Simple Token Authentication Plugin for CakePHP 4 REST API-s.

## Prerequisites

You have a database table for the users, where you have a field name `token`.

## Authentication

The plugin authentication workflow is the following.

At your client appliacation you should send a POST request to `/users/login.json` with a JSON object like this.

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

Than you can use this token to authenticate yourself for accessing urls what requires authentication. The token should be sent in a request header named `Token`.

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
use ApiTokenAuthenticator\Plugin as ApiTokenAuthenticator;

$this->addPlugin(ApiTokenAuthenticator::class);
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
