<?php

namespace Outstack\Enveloper\Templates\Loader\ConfigurationParser;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class TemplateConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('template');
        $root
            ->children()
                ->scalarNode('subject')->end()
                ->arrayNode('from')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return ['email' => $v]; })
                    ->end()
                    ->children()
                        ->scalarNode('name')->defaultNull()->end()
                        ->scalarNode('email')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('recipients')
                    ->children()
                        ->arrayNode('to')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) { return ['email' => $v]; })
                                ->end()
                                ->children()
                                    ->scalarNode('name')->defaultNull()->end()
                                    ->scalarNode('email')->isRequired()->end()
                                    ->scalarNode('iterateOver')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cc')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) { return ['email' => $v]; })
                                ->end()
                                ->children()
                                    ->scalarNode('name')->defaultNull()->end()
                                    ->scalarNode('email')->isRequired()->end()
                                    ->scalarNode('iterateOver')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('bcc')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) { return ['email' => $v]; })
                                ->end()
                                ->children()
                                    ->scalarNode('name')->defaultNull()->end()
                                    ->scalarNode('email')->isRequired()->end()
                                    ->scalarNode('iterateOver')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('content')
                    ->children()
                        ->scalarNode('text')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('html')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;

    }
}