<?php
/*
* This file is part of the notification-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\NotificationBundle\Backend;

use Sonata\NotificationBundle\Backend\MessageManagerBackend;
use Sonata\NotificationBundle\Iterator\MessageIteratorInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
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