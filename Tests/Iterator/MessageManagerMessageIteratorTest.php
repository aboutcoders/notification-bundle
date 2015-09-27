<?php

namespace Abc\Bundle\NotificationBundle\Tests\Iterator;

use Abc\Bundle\NotificationBundle\Iterator\MessageManagerMessageIterator;
use Sonata\NotificationBundle\Model\Message;
use Sonata\NotificationBundle\Model\MessageInterface;
use Sonata\NotificationBundle\Model\MessageManagerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class MessageManagerMessageIteratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MessageManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $manager;
    /** @var MessageManagerMessageIterator */
    private $subject;

    public function setUp()
    {
        $this->manager = $this->getMock('Sonata\NotificationBundle\Model\MessageManagerInterface');
        $this->subject = new MessageManagerMessageIterator($this->manager, array('type'), 10);
    }

    public function testGetManager()
    {
        $this->assertSame($this->manager, $this->subject->getManager());
    }

    /**
     * @param $numOfEntities
     * @param $types
     * @param $pageSize
     * @dataProvider getIteratorData

    public function testIterator($numOfEntities, $types, $pageSize)
    {
        $expectationMap = $this->buildExpectationMap($numOfEntities);

        $subject  = new MessageManagerMessageIterator($this->manager, $types, $pageSize);
        $entities = $this->createEntities($numOfEntities);

        $this->initManager($entities, $types, $pageSize);

        foreach($subject as $message)
        {
            if(array_key_exists($message->getType(), $expectationMap))
            {
                unset($expectationMap[$message->getType()]);
            }
        }

        $this->assertEmpty($expectationMap);
    } */

    public function testKey()
    {
        $subject  = new MessageManagerMessageIterator($this->manager, 10);
        $entities = $this->createEntities(5);

        $this->initManager($entities, array(), 10);

        $this->assertEmpty(0, $subject->key());
    }

    public function testValid()
    {
        $subject = new MessageManagerMessageIterator($this->manager, 10);
        $this->initManager(array(), array(), 10);

        $this->assertFalse($subject->valid());
    }


    public static function getIteratorData()
    {
        return array(
            array(10, array(), 10),
            array(10, array(), 1),
            array(10, array(), 15),
            array(15, array(), 10),
        );
    }

    private function initManager(array $messages, $types, $batchSize)
    {
        $this->manager->expects($this->any())
            ->method('findByTypes')
            ->with($types, MessageInterface::STATE_OPEN, $batchSize)
            ->willReturnCallback(
                    function ($types, $state, $batchSize) use ($messages)
                    {
                        return array_slice($messages, null, $batchSize);
                    }
            );
    }

    private function createEntities($num)
    {
        $entities = array();
        for($i = 0; $i < $num; $i++)
        {
            $entity = new Message();
            $entity->setType($i + 1);

            $entities[] = $entity;
        }

        return $entities;
    }

    private function buildExpectationMap($numOfEntities)
    {
        $map = array();
        for($i = 1; $i <= $numOfEntities; $i++)
        {
            $map[$i] = null;
        }

        return $map;
    }
}