<?php
declare(strict_types=1);

use Cake\TestSuite\Fixture\SchemaLoader;
// Load a schema dump file.

(new SchemaLoader())->loadSqlFiles('tests/schema.sql', 'test');
