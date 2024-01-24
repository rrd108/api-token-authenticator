# CakePHP ApiTokenAuthenticator Plugin

A Simple Token Authentication Plugin for CakePHP 5 REST API-s.

For CakePHP 4 version see [rrd108/api-token-authenticator](https://github.com/rrd108/api-token-authenticator/tree/cakephp-4)

For a REST API you may want to use a cors plugin like [rrd108/cakephp-cors](https://github.com/rrd108/cakephp-cors) and a json api exception plugin like [rrd108/cakephp-json-api-exception](https://github.com/rrd108/cakephp-json-api-exception).

If you use vuejs as your frontend you may wnat to bake your vue components with [rrd108/vue-bake](https://github.com/rrd108/vue-bake).

## Configuration

### `Users` table

In your `users` table you should have a field named `token`, or whatever name you choose for the token. We will use `token` in the examples. The `token` value will not be automatically generated by the plugin. You can generate it in your `UsersController.php` file's `login()` method (or elsewhere if you want). See the example below.

### Changing the default settings

If you are happy with the default settings, you can skip this section.

For defaults see `config/apiTokenAuthenticator.php` file in the plugin's directory.

If you want to change any of the values then create your own `config/apiTokenAuthenticator.php` file at your project's `config` directory. In your config file, you should use _only_ those keys that you want to change. It will be merged to the default one. So, for example, if you are happy with all the options, except in your case the token's header name is `Authorization`, then you have to put this into your on config file.

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

At your client appliacation you should send a POST request to `/users/login.json` (or what you set in your `config/apiTokenAuthenticator.php` file) with a JSON object like this.

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

Than you can use this `token` to authenticate yourself for accessing urls what requires authentication. The `token` should be sent in a request header named `Token` (or what you set in your `config/apiTokenAuthenticator.php` file).

## Installation

### 1. Install the plugin

Including the plugin is pretty much as with every other CakePHP plugin:

```bash
composer require rrd108/api-token-authenticator
```

Then, to load the plugin either run the following command:

```bash
bin/cake plugin load ApiTokenAuthenticator
```

or manually add the following line to your app's `config/plugins.php`:

```php
return [
  // other plugins
  'ApiTokenAuthenticator' => [],
];
```

### 2. Disable CSRF protection

You should comment out (or delete) `CsrfProtectionMiddleware` in your `/src/Application.php` file's `middleware()` method.

### 3. Load the plugin's components

At your `AppController.php` file's `initialize()` function you should include these components:

```php
public function initialize(): void
{
  parent::initialize();
  $this->loadComponent('Authentication.Authentication');
}
```

And add JSON view support to `AppController.php`.

```php
use Cake\View\JsonView;

public function viewClasses(): array
{
  return [JsonView::class];
}
```

### 4. Set password hasher

Update your `src/Model/Entity/User.php` file adding the following.

```php
use Authentication\PasswordHasher\DefaultPasswordHasher;
protected function _setPassword(string $password)
{
  $hasher = new DefaultPasswordHasher();
  return $hasher->hash($password);
}
```

Do not forget to remove the `token` field from the `$_hidden` array.

### 5. Set extensions for `routes`

As you probably will use JSON urls, do not forget to add this line to your `config/routes.php` file.

```php
$routes->scope('/', function (RouteBuilder $builder): void {
  // other routes
  $builder->setExtensions(['json']);
  $builder->resources('Users');

  $builder->fallbacks();
});
```

### 5. Set JSON response in controllers

In your controllers you should set the JSON response type.

```php
// for example in UsersController.php
public function index()
{
  $query = $this->Users->find();
  $users = $this->paginate($query);

  $this->set(compact('users'));
  $this->viewBuilder()->setOption('serialize', ['users']);
}
```

As CakePHP response use content type negotiation it is important to add the `Accept: application/json` header to your requests.

That's it. It should be up and running.

## The `login()` method

### If you use static `tokens`

Login method is not added automatically, you should implement it. Here is an example how.

```php
public function login()
{
  $result = $this->Authentication->getResult();
  if ($result->isValid()) {
    $user = $this->Authentication->getIdentity()->getOriginalData();
    $this->set(compact('user'));
    $this->viewBuilder()->setOption('serialize', ['user']);
  }
}
```

The `login` method should be added to the list of actions that are allowed to be accessed without authentication.

```php
public function beforeFilter(\Cake\Event\EventInterface $event)
{
  parent::beforeFilter($event);
  $this->Authentication->allowUnauthenticated(['login']);
}
```

### If you use dynamic `tokens`

```php
public function login()
{
  $result = $this->Authentication->getResult();
  if ($result->isValid()) {
    $user = $this->Authentication->getIdentity()->getOriginalData();
    $user->token = $this->generateToken();
    $user = $this->Users->save($user);
    $user = $this->Users->get($user->id);

    $this->set(compact('user'));
    $this->viewBuilder()->setOption('serialize', ['user']);
  }
  // if login failed you can throw an exception, suggested: rrd108/cakephp-json-api-exception
}

private function generateToken(int $length = 36)
{
  $random = base64_encode(Security::randomBytes($length));
  $cleaned = preg_replace('/[^A-Za-z0-9]/', '', $random);
  return substr($cleaned, 0, $length);
}
```

## Token expiration

By default tokens are not invalidated by the plugin, you can use them permanently or as long as there is no new login session like in the example code above.

If you want the plugin to use tokens only for a certain period of time, you should do the following steps.

1. Add a column to your `users` table named `token_expiration` and set it's type to `datetime`. You can use a different field name, but you have to change it in the following steps.

2. In your `config/apiTokenAuthenticator.php` file set `'tokenExpiration' => 'token_expiration'`.

3. Update your `src/Model/Entity/User.php` file adding the field to the `$accessible` array.

```php
protected $_accessible = [
  'email' => true,
  // your other fields here
  'token' => true,
  'token_expiration' => true,
];
```

4. Update your `src/Model/Table/UsersTable.php` file adding the following.

```php
$validator
  ->dateTime('token_expiration')
  ->allowEmptyDateTime('token_expiration');
```

5. In your `src/Controller/UsersController.php` file you should modify `login()` method.

```php
public function login()
{
  $result = $this->Authentication->getResult();
  if ($result->isValid()) {
    $user = $this->Authentication->getIdentity()->getOriginalData();
    list($user->token, $user->token_expiration) = $this->generateToken();
    $user = $this->Users->save($user);

    $this->set(compact('user'));
    $this->viewBuilder()->setOption('serialize', ['user']);

    // delete all expired tokens
    $this->Users->updateAll(
      ['token' => null, 'token_expiration' => null],
      ['token_expiration <' => Chronos::now()]
    );
  }
}

private function generateToken(int $length = 36, string $expiration = '+6 hours')
{
  $random = base64_encode(Security::randomBytes($length));
  $cleaned = preg_replace('/[^A-Za-z0-9]/', '', $random);
  return [$cleaned, strtotime($expiration)];
}
```

## Access without authentication

If you want to let the users to access a resource without authentication you should state it in the controller's `beforeFilter()` method. The `login`, `register` methods are good candidates to allow unauthenticated access.

```php
// in UsersController.php
public function beforeFilter(\Cake\Event\EventInterface $event)
{
  parent::beforeFilter($event);
  $this->Authentication->allowUnauthenticated(['login', 'index']);
}
```

This will allow users to access `/users/login.json` and `/users.json` url without authentication.
