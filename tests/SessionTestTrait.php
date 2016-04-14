<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\ServerRequestFactory;

trait SessionTestTrait
{

    public function mockSession()
    {
        $this->session = $this->getMockBuilder('Aura\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = ServerRequestFactory::fromGlobals()
            ->withAttribute('aura/session:session', $this->session);
    }

    public function mockSegment()
    {
        $this->segment = $this->getMockBuilder('Aura\Session\Segment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session->expects($this->once())
            ->method('getSegment')
            ->with($this->equalTo('vperyod/session-handler:messages'))
            ->will($this->returnValue($this->segment));
    }

}
