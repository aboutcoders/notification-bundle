<?php

namespace Abc\Bundle\NotificationBundle\Tests\Fixtures\SonataNotificationBundle\Entity;

use Sonata\NotificationBundle\Entity\BaseMessage as BaseMessage;

class Message extends BaseMessage
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}