<?php
declare(strict_types=1);

namespace ApiTokenAuthenticator\Authentication\Authenticator;

use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\Authenticator\TokenAuthenticator;
use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Psr\Http\Message\ServerRequestInterface;

class ProvisoryTokenAuthenticator extends TokenAuthenticator
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $result = parent::authenticate($request);
        $user = $result->getData();
        if (!$user) {
            return $result;
        }

        $options = Configure::read('ApiTokenAuthenticator');

        if (isset($options['tokenExpiration']) && $result->getData()[$options['tokenExpiration']] < DateTime::now()) {
            return new Result(null, 'TOKEN_EXPIRED');
        }

        return new Result($user, Result::SUCCESS);
    }
}
