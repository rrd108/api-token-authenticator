<?php

namespace ApiTokenAuthenticator\Authentication\Authenticator;

use Authentication\Authenticator\Result;
use Psr\Http\Message\ServerRequestInterface;
use Authentication\Authenticator\ResultInterface;
use Authentication\Authenticator\TokenAuthenticator;
use Cake\I18n\FrozenTime;

class ProvisoryTokenAuthenticator extends TokenAuthenticator
{
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $result = parent::authenticate($request);
        $user = $result->getData();
        if (!$user) {
            return $result;
        }

        if ($result->getData()['token_expiration'] < FrozenTime::now()) {
            return new Result(null, 'TOKEN_EXPIRED');
        }

        return new Result($user, Result::SUCCESS);
    }
}
