<?php

declare(strict_types=1);

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;
use Cake\Utility\Security;

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

//require_once 'vendor/cakephp/cakephp/src/functions.php';
//require_once 'vendor/autoload.php';

define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
// define('APP', ROOT . 'App' . DS);
// define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', ROOT . DS . 'config' . DS);

Configure::write('debug', true);
Configure::write(
    'App', [
    'namespace' => 'TestApp',
    'encoding' => 'UTF-8',
    'paths' => [
        'plugins' => [ROOT . 'Plugin' . DS],
        'templates' => [ROOT . 'templates' . DS],
    ],
    ]
);

ConnectionManager::setConfig(
    'test', [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Sqlite',
    'database' => ':memory:',
    'encoding' => 'utf8',
    'timezone' => 'UTC',
    'quoteIdentifiers' => false,
    ]
);

Router::reload();
//Security::setSalt('rrd');

Plugin::getCollection()->add(new \Authentication\Plugin());

$_SERVER['PHP_SELF'] = '/';

Configure::load('ApiTokenAuthenticator.apiTokenAuthenticator');

use Cake\TestSuite\Fixture\SchemaLoader;
// Load a schema dump file.
(new SchemaLoader())->loadSqlFiles('tests/schema.sql', 'test');
