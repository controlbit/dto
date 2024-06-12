<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface ConstructorStrategyInterface
{
    /**
     * @param  \ReflectionClass<object>  $destinationReflectionClass
     */
    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void;

    /**
     * @param  \ReflectionClass<object>  $reflectionClass
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        \ReflectionClass      $reflectionClass,
    ): object;

    public function getName(): string;
}