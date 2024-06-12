<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final class NeverStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'never';

    public function __construct(private readonly bool $mapPrivateProperties)
    {
    }

    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void {

        if ($this->mapPrivateProperties) {
            return;
        }

        if ($this->hasPublicMembers($destinationReflectionClass)) {
            return;
        }

        throw new InvalidArgumentException(
            'All members are private, but "map_private_properties" is not set..'
        );
    }

    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        \ReflectionClass      $reflectionClass,
    ): object {
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param  \ReflectionClass<object>  $reflectionClass
     */
    private function hasPublicMembers(\ReflectionClass $reflectionClass): bool
    {
        $numberOfPublicProperties = \array_filter(
            $reflectionClass->getProperties(),
            static fn (\ReflectionProperty $property) => $property->isPublic() && !$property->isStatic()
        );

        $numberOfPublicMethods = \array_filter(
            $reflectionClass->getMethods(),
            static fn (\ReflectionMethod $method) => $method->isPublic() && !$method->isStatic()
        );

        return 0 !== ($numberOfPublicProperties + $numberOfPublicMethods);
    }
}