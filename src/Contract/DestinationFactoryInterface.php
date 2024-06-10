<?php
declare(strict_types=1);
namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface DestinationFactoryInterface
{
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        ?string               $destination,
    ): object|string|null;

}