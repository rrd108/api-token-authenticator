<?php

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierCollection;
use ApiTokenAuthenticator\Authentication\Authenticator\ProvisoryTokenAuthenticator;

class ProvisoryTokenAuthenticatorTest extends TestCase
{
    public function testAuthenticateViaHeaderToken()
    {
        $identifiers = new IdentifierCollection([
            'Authentication.Token' => [
                'header' => 'Token',
            ],
        ]);
        $request = new ServerRequest();
        //$request->withAddedHeader('Token', 'token-1');
        $options = Configure::read('ApiTokenAuthenticator');

        $tokenAuth = new ProvisoryTokenAuthenticator($identifiers, $options);

        $result = $tokenAuth->authenticate($request);
        $this->assertSame(Result::FAILURE_CREDENTIALS_MISSING, $result->getStatus());
    }
}
