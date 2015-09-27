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
} 