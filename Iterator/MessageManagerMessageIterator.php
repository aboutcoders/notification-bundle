<?php

namespace Abc\Bundle\NotificationBundle\Iterator;

use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;
use Sonata\NotificationBundle\Model\Message;
use Sonata\NotificationBundle\Model\MessageInterface;
use Sonata\NotificationBundle\Model\MessageManagerInterface;

/**
 * Custom implementation of MessageIteratorInterface that does not sleep if manager does not find any messages.
 *
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class MessageManagerMessageIterator implements MessageIteratorInterface
{

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var Message
     */
    protected $current;

    /**
     * @var int
     */
    protected $counter;

    /**
     * @var
     */
    protected $position;

    /**
     * @var array
     */
    protected $types;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @var array
     */
    protected $buffer = array();

    /**
     * @param MessageManagerInterface $messageManager
     * @param array                   $types
     * @param int                     $batchSize
     */
    public function __construct(MessageManagerInterface $messageManager, $types = array(), $batchSize = 10)
    {
        $this->messageManager = $messageManager;
        $this->counter        = 0;
        $this->types          = $types;
        $this->batchSize      = $batchSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
        $this->setCurrent();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->current != null;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->setCurrent();
        $this->position++;
    }

    /**
     * Return true if the internal buffer is empty.
     *
     * @return bool
     */
    public function isBufferEmpty()
    {
        return 0 === count($this->buffer);
    }

    /**
     * Assign current pointer
     */
    protected function setCurrent()
    {
        if(count($this->buffer) === 0)
        {
            $this->buffer();
        }

        $this->current = count($this->buffer) > 0 ? array_pop($this->buffer) : null;
    }

    /**
     * Fill the inner buffer
     */
    protected function buffer()
    {
        $this->buffer = $this->messageManager->findByTypes($this->types, MessageInterface::STATE_OPEN, $this->batchSize);
    }
}