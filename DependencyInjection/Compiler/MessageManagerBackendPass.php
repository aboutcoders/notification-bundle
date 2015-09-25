<?php

namespace Abc\Bundle\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * A Compiler pass that injects a custom iterator into the doctrine message manager backend.
 *
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class MessageManagerBackendPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition('sonata.notification.backend.doctrine'))
        {
            return;
        }

        $definition = $container->getDefinition('sonata.notification.backend.doctrine');

        // iterate over doctrine backends
        foreach($definition->getArgument(3) as $pos => $qBackends)
        {
            if(is_array($qBackends) && isset($qBackends['backend']))
            {
                /** @var Reference $qBackend */
                $qBackend = $qBackends['backend'];
                $backendId = $qBackend->__toString();

                /** @var Definition $qBackendDefinition */
                $qBackendDefinition = $container->getDefinition($backendId);

                // create a custom iterator
                $iteratorId = $this->createIteratorDefinition($container, $definition->getArgument(0), $qBackendDefinition->getArgument(4), $qBackendDefinition->getArgument(5));

                // decorate backend with custom iterator
                $iteratorAwareBackendDefinition = new Definition('Abc\Bundle\NotificationBundle\Backend\IteratorAwareMessageManagerBackend', $qBackendDefinition->getArguments());
                $iteratorAwareBackendDefinition->addMethodCall('setIterator', array(new Reference($iteratorId)));

                // replace default backend with custom backend
                $container->setDefinition($backendId, $iteratorAwareBackendDefinition);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param Reference        $manager The reference to the message manager within the container
     * @param int              $batchSize
     * @param array            $types
     * @return string The id of the created definition within the container
     */
    protected function createIteratorDefinition(ContainerBuilder $container, $manager, $batchSize, $types)
    {
        $iteratorId = 'application.sonata.notification.doctrine.message_iterator';
        $definition = new Definition('Abc\Bundle\NotificationBundle\Iterator\MessageManagerMessageIterator', array($manager, $types, $batchSize));
        $definition->setPublic(false);

        $container->setDefinition($iteratorId, $definition);

        if(!$container->hasParameter('abc.notification.process_control') || !$container->getParameter('abc.notification.process_control'))
        {
            return $iteratorId;
        }

        $controlledIteratorId = 'application.sonata.notification.doctrine.controlled_message_iterator';
        $definition           = new Definition(
            'Abc\Bundle\NotificationBundle\Iterator\ControlledMessageIterator',
            array(new Reference('abc.process_control.controller'), new Reference($iteratorId))
        );
        $definition->setPublic(false);

        $container->setDefinition($controlledIteratorId, $definition);

        return $controlledIteratorId;
    }
} 