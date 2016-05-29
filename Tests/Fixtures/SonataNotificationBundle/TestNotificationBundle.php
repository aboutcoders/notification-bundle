<?php

namespace Abc\Bundle\NotificationBundle\Tests\Fixtures\SonataNotificationBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class TestNotificationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'AbcNotificationBundle';
    }
}