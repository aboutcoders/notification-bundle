<?php

namespace Abc\Bundle\NotificationBundle\Iterator;

use Abc\ProcessControl\Controller;
use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;

/**
 * A message iterator controlled by a Abc\ProcessControl\Controller.
 *
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 * @see Abc\ProcessControl\Controller
 */
class ControlledMessageIterator implements MessageIteratorInterface
{
    /** @var Controller */
    private $controller;
    /** @var MessageIteratorInterface */
    private $messageIterator;

    /**
     * @param Controller               $controller
     * @param MessageIteratorInterface $smessageIterator
     */
    function __construct(Controller $controller, MessageIteratorInterface $smessageIterator)
    {
        $this->controller      = $controller;
        $this->messageIterator = $smessageIterator;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->messageIterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->messageIterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->messageIterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return !$this->controller->doExit() && $this->messageIterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->messageIterator->rewind();
    }

    /**
     * Return true if the internal buffer is empty.
     *
     * @return bool
     * @throws \Exception If method not implemented by message given iterator
     * @see Sonata\NotificationBundle\Iterator\MessageManagerMessageIterator
     */
    public function isBufferEmpty()
    {
        if(!method_exists($this->messageIterator, 'isBufferEmpty'))
        {
            throw new \Exception(sprintf('Call to undefined method %s->isBufferEmpty', get_class($this->messageIterator)));
        }

        return $this->messageIterator->isBufferEmpty();
    }
} 