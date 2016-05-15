<?php
/*
* This file is part of the notification-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\NotificationBundle\Tests\Backend;

use Abc\Bundle\NotificationBundle\Backend\IteratorAwareMessageManagerBackend;
use Sonata\NotificationBundle\Backend\MessageManagerBackend;
use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;
use Sonata\NotificationBundle\Model\MessageManagerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class IteratorAwareMessageManagerBackendTest extends \PHPUnit_Framework_TestCase
{
    private $iterator;

    /** @var IteratorAwareMessageManagerBackend */
    private $subject;

    public function setUp()
    {
        $this->iterator = $this->getMock('Sonata\NotificationBundle\Iterator\MessageIteratorInterface');

        $messageManager = $this->getMock('Sonata\NotificationBundle\Model\MessageManagerInterface');

        $this->subject = new IteratorAwareMessageManagerBackend($messageManager, array());
    }

    public function testIsChildOfMessageManagerBackend()
    {
        $this->assertInstanceOf('Sonata\NotificationBundle\Backend\MessageManagerBackend', $this->subject);
    }

    public function testSetGetIterator()
    {
        $this->assertNotNull($this->subject->getIterator());

        $this->subject->setIterator($this->iterator);

        $this->assertSame($this->iterator, $this->subject->getIterator());
    }
}