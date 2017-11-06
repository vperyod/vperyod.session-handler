<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\ServerRequestFactory;

trait SessionTestTrait
{

    protected $messages;

    protected $session;


    public function mockSession()
    {
        $this->session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = ServerRequestFactory::fromGlobals()
            ->withAttribute(SessionHandler::SESSION_ATTRIBUTE, $this->session);
    }
}
