<?php
declare(strict_types=1);

namespace ControlBit\Dto\Destination;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\ConstructorStrategy\StrategyCollection;
use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Contract\DestinationFactoryInterface;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final readonly class ConstructedDelegate implements DestinationFactoryInterface
{
    public function __construct(
        private StrategyCollection $constructorStrategyCollection,
    ) {
    }

    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        ?string               $destination,
    ): object|string|null {
        if (null === $destination || !\class_exists($destination)) {
            return null;
        }

        /** @var ?Dto $dtoAttribute */
        $dtoAttribute          = $sourceClassMetadata->getAttributes()->get(Dto::class);
        $destinationReflection = new \ReflectionClass($destination);
        $constructorStrategy   = $this->getConstructorStrategy($dtoAttribute);

        $constructorStrategy->validate($destinationReflection, $sourceMapMetadataCollection);

        return $constructorStrategy->create(
            $mapper,
            $source,
            $sourceClassMetadata,
            $sourceMapMetadataCollection,
            $destinationReflection,
        );
    }

    private function getConstructorStrategy(Dto|null $dtoAttribute): ConstructorStrategyInterface
    {
        if (null === $dtoAttribute) {
            return $this->constructorStrategyCollection->getDefaultStrategy();
        }

        return $this->constructorStrategyCollection->getStrategy($dtoAttribute->getConstructorStrategy());
    }
}