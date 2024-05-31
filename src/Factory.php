<?php
declare(strict_types=1);

namespace ControlBit\Dto;

use ControlBit\Dto\CaseTransformer\SnakeCaseToCamelCaseTransformer;
use ControlBit\Dto\Contract\CaseTransformerInterface;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Factory\DestinationFactory;
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
    /**
     * @param  class-string  $caseTransformer
     */
    public static function create(
        bool                $mapPrivateProperties = true,
        string              $caseTransformer = SnakeCaseToCamelCaseTransformer::class,
        ConstructorStrategy $constructorStrategy = ConstructorStrategy::OPTIONAL,
    ): Mapper {
        if (!\class_exists($caseTransformer)) {
            throw new InvalidArgumentException(
                \sprintf('Transformer class "%s" does not exist.', $caseTransformer)
            );
        }

        /** @var CaseTransformerInterface $caseTransformer */
        $caseTransformer         = new $caseTransformer();
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

        $valueConverter     = new ValueConverter(
            [
                new ArrayOfDto(),
                new ArrayToObject(),
                new EntityIdentifier(),
            ]
        );
        $destinationFactory = new DestinationFactory($valueConverter, $constructorStrategy);

        return new Mapper(
            $objectMetadataFactory,
            $mapMetadataFactory,
            $destinationFactory,
            $valueConverter,
            $setterFinder
        );
    }
}