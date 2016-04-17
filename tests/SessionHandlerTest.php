<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function testHandler()
    {
        $cookie = ['data'];

        $this->session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getMockBuilder('Aura\Session\SessionFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())
            ->method('newInstance')
            ->with($cookie)
            ->will($this->returnValue($this->session));

        $handler = new SessionHandler($factory);
        $handler->setSessionAttribute('session');

        $handler(
            ServerRequestFactory::fromGlobals()->withCookieParams($cookie),
            new Response(),
            [$this, 'checkRequest']
        );
    }

    public function checkRequest($request, $response)
    {
        $this->assertSame(
            $this->session,
            $request->getAttribute('session')
        );

        return $response;
    }
}
