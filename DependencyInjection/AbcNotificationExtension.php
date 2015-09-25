<?php

namespace Abc\Bundle\NotificationBundle\DependencyInjection;

use Abc\Bundle\EnumBundle\Serializer\Handler\EnumHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class AbcNotificationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('abc.notification.process_control', $config['process_control']);
    }
} 