<?php
// @codingStandardsIgnoreFile

namespace Vperyod\SessionHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class MessengerTest extends \PHPUnit_Framework_TestCase
{
    protected $segment;

    public function setUp()
    {
        parent::setUp();

        $this->segment = $this->getMockBuilder('Aura\Session\Segment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->messenger = new Messenger($this->segment);
    }

    public function messageProvider()
    {
        return [
            // method   params          expect[msg, lvl]
            ['add'    , ['foo'],        ['foo', 'info']],
            ['add'    , ['foo', 'bar'], ['foo', 'bar']],
            ['success', ['YES'],        ['YES', 'success']],
            ['info'   , ['MAYBE'],      ['MAYBE', 'info']],
            ['warning', ['NO'],         ['NO', 'warning']]
        ];
    }

    public function expectMessage($msg, $lvl)
    {
        $this->segment->expects($this->once())
            ->method('addFlash')
            ->with(
                $this->equalTo(
                    [
                        'message' => $msg,
                        'level'   => $lvl
                    ]
                )
            );
    }

    /**
     * @dataProvider messageProvider
     */
    public function testMessenger($method, $params, $expect)
    {
        call_user_func_array([$this, 'expectMessage'], $expect);
        $this->assertSame(
            $this->messenger,
            call_user_func_array([$this->messenger, $method], $params)
        );
    }

    public function testGet()
    {
        $this->segment->expects($this->once())
            ->method('getAllCurrentFlash')
            ->will($this->returnValue('foo'));

        $this->assertEquals(
            'foo',
            $this->messenger->getMessages()
        );
    }
}
