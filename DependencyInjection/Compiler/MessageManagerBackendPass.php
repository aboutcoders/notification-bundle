<?php
/*
* This file is part of the notification-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\NotificationBundle\DependencyInjection\Compiler;

use Abc\Bundle\NotificationBundle\Backend\IteratorAwareMessageManagerBackend;
use Abc\Bundle\NotificationBundle\Iterator\ControlledMessageIterator;
use Abc\Bundle\NotificationBundle\Iterator\MessageManagerMessageIterator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * A Compiler pass that injects a custom iterator into the doctrine message manager backend.
 *
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class MessageManagerBackendPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $backendService;

    /**
     * @var string
     */
    protected $iteratorService;

    /**
     * @param string $backendService
     * @param string $iteratorService
     */
    public function __construct(
        $backendService = 'sonata.notification.backend.doctrine',
        $iteratorService = 'application.sonata.notification.doctrine.message_iterator'
    )
    {
        $this->backendService  = $backendService;
        $this->iteratorService = $iteratorService;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->backendService)) {
            return;
        }

        $definition = $container->getDefinition($this->backendService);

        // iterate over doctrine backends
        foreach ($definition->getArgument(3) as $pos => $qBackends) {
            if (is_array($qBackends) && isset($qBackends['backend'])) {
                /** @var Reference $qBackend */
                $qBackend  = $qBackends['backend'];
                $backendId = $qBackend->__toString();

                /** @var Definition $qBackendDefinition */
                $qBackendDefinition = $container->getDefinition($backendId);

                // create a custom iterator
                $iteratorId = $this->createIteratorDefinition($container, $definition->getArgument(0), $qBackendDefinition->getArgument(4), $qBackendDefinition->getArgument(5));

                // decorate backend with custom iterator
                $iteratorAwareBackendDefinition = new Definition(IteratorAwareMessageManagerBackend::class, $qBackendDefinition->getArguments());
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
     * @return string The service id of the created iterator
     */
    protected function createIteratorDefinition(ContainerBuilder $container, $manager, $batchSize, $types)
    {
        $iteratorId = $this->iteratorService;
        $definition = new Definition(MessageManagerMessageIterator::class, array($manager, $types, $batchSize));
        $definition->setPublic(false);

        $container->setDefinition($iteratorId, $definition);

        if (!$container->has('abc.process_control.controller')) {
            return $iteratorId;
        }

        $controlledIteratorId = 'application.sonata.notification.doctrine.controlled_message_iterator';
        $definition           = new Definition(ControlledMessageIterator::class, [
            new Reference('abc.process_control.controller'),
            new Reference($iteratorId)
        ]);
        $definition->setPublic(false);

        $container->setDefinition($controlledIteratorId, $definition);

        return $controlledIteratorId;
    }
}