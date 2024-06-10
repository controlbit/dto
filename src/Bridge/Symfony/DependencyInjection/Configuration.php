<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bridge\Symfony\DependencyInjection;

use ControlBit\Dto\CaseTransformer\SnakeCaseToCamelCaseTransformer;
use ControlBit\Dto\Enum\ConstructorStrategy;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dto_bundle');

        /**
         * @phpstan-ignore-next-line
         */
        $treeBuilder
            ->getRootNode()
                ->children()
                    ->booleanNode('validation_json_bad_request')
                        ->info('Send default bad request when JSON request is made, when argument validation fail.')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('case_transformer')
                        ->defaultValue(SnakeCaseToCamelCaseTransformer::class)
                        ->info('ID of service for transforming request key case, to camel case. From example, from Snake Case.')
                    ->end()
                    ->booleanNode('map_private_properties')
                        ->defaultValue(true)
                        ->info('Should map private properties directly, using reflection.')
                    ->end()
                    ->enumNode('constructor_strategy')
                        ->values(ConstructorStrategy::all())
                        ->defaultValue(ConstructorStrategy::OPTIONAL->value)
                        ->info('Default constructor strategy. Can be overridden via Attribute.')
                    ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}