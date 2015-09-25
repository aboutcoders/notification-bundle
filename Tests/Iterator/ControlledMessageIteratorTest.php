<?php

namespace Abc\Bundle\NotificationBundle\Tests\Iterator;

use Abc\Bundle\NotificationBundle\Iterator\ControlledMessageIterator;
use Abc\ProcessControl\Controller;
use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ControlledMessageIteratorTest extends \PHPUnit_Framework_TestCase
{

    /** @var Controller|\PHPUnit_Framework_MockObject_MockObject */
    private $controller;
    /** @var MessageIteratorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $iterator;
    /** @var ControlledMessageIterator */
    private $subject;

    public function setUp()
    {
        $this->controller = $this->getMock('Abc\ProcessControl\Controller');
        $this->iterator  = $this->getMock('Sonata\NotificationBundle\Iterator\MessageIteratorInterface');
        $this->subject = new ControlledMessageIterator($this->controller, $this->iterator);
    }

    public function testCurrent()
    {
        $this->iterator->expects($this->once())
            ->method('current')
            ->will($this->returnValue('foo'));

        $this->assertEquals('foo', $this->subject->current());
    }

    public function testNext()
    {
        $this->iterator->expects($this->once())
            ->method('next');

        $this->subject->next();
    }

    public function testKey()
    {
        $this->iterator->expects($this->once())
            ->method('key')
            ->will($this->returnValue('foo'));

        $this->assertEquals('foo', $this->subject->key());
    }

    /**
     * @param bool $doExit
     * @param bool $valid
     * @dataProvider getBooleanArray
     */
    public function testValid($doExit, $valid)
    {
        $this->iterator->expects($this->any())
            ->method('valid')
            ->willReturn($valid);

        $this->controller->expects($this->any())
            ->method('doExit')
            ->willReturn($doExit);

        if($doExit)
        {
            $this->assertEquals(false, $this->subject->valid());
        }
        else
        {
            $this->assertEquals($valid, $this->subject->valid());
        }
    }

    public function testRewind()
    {
        $this->iterator->expects($this->once())
            ->method('rewind');

        $this->subject->rewind();
    }

    /**
     * @return array
     */
    public static function getBooleanArray()
    {
        return array(
            array(true, true),
            array(true, false),
            array(false, false),
            array(false, true)
        );
    }
}
 