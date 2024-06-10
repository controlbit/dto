<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final class OptionalStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'optional';

    public function shouldInjectViaConstructor(): bool
    {
        return true;
    }

    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void {
        $constructor = $destinationReflectionClass->getConstructor();

        if (null === $constructor) {
            return;
        }

        if ($constructor->getNumberOfRequiredParameters() > \count($sourceMapMetadataCollection)) {
            throw new InvalidArgumentException(
                'Not enough arguments in constructor, to be able to map with ConstructorStrategy "Always".'
            );
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}