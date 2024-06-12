<?php
declare(strict_types=1);

namespace ControlBit\Dto;

use ControlBit\Dto\ConstructorStrategy\AlwaysStrategy;
use ControlBit\Dto\ConstructorStrategy\NeverStrategy;
use ControlBit\Dto\ConstructorStrategy\OptionalStrategy;
use ControlBit\Dto\ConstructorStrategy\StrategyCollection;
use ControlBit\Dto\Destination\ConstructedDelegate;
use ControlBit\Dto\Destination\EntityDelegate;
use ControlBit\Dto\Destination\NonConstructedDelegate;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Destination\DestinationFactory;
use ControlBit\Dto\Finder\AccessorFinder;
use ControlBit\Dto\Finder\SetterFinder;
use ControlBit\Dto\Finder\SetterType\Direct;
use ControlBit\Dto\Finder\SetterType\ViaSetter;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\Mapper\ValueConverter\ArrayOfDto;
use ControlBit\Dto\Mapper\ValueConverter\ArrayToObject;
use ControlBit\Dto\Mapper\ValueConverter\EntityIdentifier;
use ControlBit\Dto\MetaData\Class\ClassMetadataFactory;
use ControlBit\Dto\MetaData\Map\MapMetadataFactory;
use ControlBit\Dto\MetaData\Method\MethodMetadataFactory;
use ControlBit\Dto\MetaData\Property\PropertyMetadataFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
final class Factory
{
    private function __construct()
    {
        // noop
    }

    public static function create(
        bool                $mapPrivateProperties = true,
        ConstructorStrategy $constructorStrategy = ConstructorStrategy::OPTIONAL,
    ): Mapper {
        $accessorFinder          = new AccessorFinder($mapPrivateProperties);
        $propertyMetaDataFactory = new PropertyMetadataFactory($accessorFinder);
        $methodMetaDataFactory   = new MethodMetadataFactory();
        $mapMetadataFactory      = new MapMetadataFactory();
        $objectMetadataFactory   = new ClassMetadataFactory($propertyMetaDataFactory, $methodMetaDataFactory);

        $setterFinder = new SetterFinder(
            [
                new  ViaSetter(),
                new  Direct(),
            ],
        );

        $valueConverter = new ValueConverter(
            [
                new ArrayOfDto(),
                new ArrayToObject(),
                new EntityIdentifier(),
            ]
        );

        $alwaysStrategy = new AlwaysStrategy($valueConverter);
        $neverStrategy  = new NeverStrategy($mapPrivateProperties);

        $strategyCollection = new StrategyCollection(
            [
                $alwaysStrategy,
                $neverStrategy,
                new OptionalStrategy($alwaysStrategy, $neverStrategy, $mapPrivateProperties),
            ],
            $constructorStrategy
        );

        $destinationFactory = new DestinationFactory(
            [
                new EntityDelegate(),
                new ConstructedDelegate($strategyCollection),
                new NonConstructedDelegate(),
            ]
        );

        return new Mapper(
            $objectMetadataFactory,
            $mapMetadataFactory,
            $destinationFactory,
            $valueConverter,
            $setterFinder
        );
    }
}