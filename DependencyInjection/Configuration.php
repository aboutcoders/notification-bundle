<?php

namespace Abc\Bundle\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('abc_notification')
            ->children()
                ->booleanNode('process_control')
                    ->defaultFalse()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
