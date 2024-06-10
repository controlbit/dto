<?php
declare(strict_types=1);

namespace ControlBit\Dto\Destination;

use ControlBit\Dto\Contract\DestinationFactoryInterface;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final readonly class NonConstructedDelegate implements DestinationFactoryInterface
{
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

        return (new \ReflectionClass($destination))->newInstanceWithoutConstructor();
    }

}