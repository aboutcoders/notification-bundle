<?php
/*
* This file is part of the notification-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\NotificationBundle\Iterator;

use Abc\ProcessControl\ControllerInterface;
use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;

/**
 * A message iterator controlled by a Abc\ProcessControl\Controller.
 *
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 * @see    Abc\ProcessControl\Controller
 */
class ControlledMessageIterator implements MessageIteratorInterface
{
    /**
     * @var ControllerInterface
     */
    private $controller;

    /**
     * @var MessageIteratorInterface
     */
    private $messageIterator;

    /**
     * @param ControllerInterface      $controller
     * @param MessageIteratorInterface $messageIterator
     */
    function __construct(ControllerInterface $controller, MessageIteratorInterface $messageIterator)
    {
        $this->controller      = $controller;
        $this->messageIterator = $messageIterator;
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
        if (!method_exists($this->messageIterator, 'isBufferEmpty')) {
            throw new \Exception(sprintf('Call to undefined method %s->isBufferEmpty', get_class($this->messageIterator)));
        }

        return $this->messageIterator->isBufferEmpty();
    }
}