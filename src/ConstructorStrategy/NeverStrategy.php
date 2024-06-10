<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final class NeverStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'never';

    public function shouldInjectViaConstructor(): bool
    {
        return false;
    }

    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void {
    }

    public function getName(): string
    {
        return self::NAME;
    }
}