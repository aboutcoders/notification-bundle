<?php

namespace Abc\Bundle\NotificationBundle\Backend;

use Sonata\NotificationBundle\Backend\MessageManagerBackend;
use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class IteratorAwareMessageManagerBackend extends MessageManagerBackend
{
    /**
     * @var MessageIteratorInterface
     */
    protected $iterator;

    /**
     * @param MessageIteratorInterface $iterator
     */
    public function setIterator(MessageIteratorInterface $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->iterator == null ? parent::getIterator() : $this->iterator;
    }
}