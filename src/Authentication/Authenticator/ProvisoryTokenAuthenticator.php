<?php

namespace ApiTokenAuthenticator\Authentication\Authenticator;

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Authentication\Authenticator\Result;
use Psr\Http\Message\ServerRequestInterface;
use Authentication\Authenticator\ResultInterface;
use Authentication\Authenticator\TokenAuthenticator;

class ProvisoryTokenAuthenticator extends TokenAuthenticator
{
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $result = parent::authenticate($request);
        $user = $result->getData();
        if (!$user) {
            return $result;
        }

        $options = Configure::read('ApiTokenAuthenticator');

        if (isset($options['tokenExpiration']) && $result->getData()[$options['tokenExpiration']] < FrozenTime::now()) {
            return new Result(null, 'TOKEN_EXPIRED');
        }

        return new Result($user, Result::SUCCESS);
    }
}
