<?php

namespace Sunixzs\Availablespaceimage\DependencyInjection;

use Sunixzs\Availablespaceimage\ImageService;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
    * {@inheritdoc}
    */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;

        $rootNode = $treeBuilder->root('availablespaceimage');

        $rootNode
            ->children()
                ->scalarNode('prefix')->defaultValue(ImageService::DEFAULT_PREFIX)->end()
                ->scalarNode('output_dir')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}
