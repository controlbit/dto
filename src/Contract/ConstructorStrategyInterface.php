<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface ConstructorStrategyInterface
{
    public function shouldInjectViaConstructor(): bool;

    /**
     * @param  \ReflectionClass<object>  $destinationReflectionClass
     */
    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void;

    public function getName(): string;
}