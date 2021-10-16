<?php

use Cake\Core\Configure;

Configure::load('ApiTokenAuthenticator.apiTokenAuthenticator');
try {
    Configure::load('apiTokenAuthenticator');
} catch (Exception $exception) {
    //debug($exception->getMessage());
}
