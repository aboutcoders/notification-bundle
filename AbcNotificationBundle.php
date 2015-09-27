<?php

namespace Abc\Bundle\NotificationBundle;

use Abc\Bundle\NotificationBundle\DependencyInjection\Compiler\MessageManagerBackendPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class AbcNotificationBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MessageManagerBackendPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataNotificationBundle';
    }
}