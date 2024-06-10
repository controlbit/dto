<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final class AlwaysStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'always';

    public function shouldInjectViaConstructor(): bool
    {
        return true;
    }

    public function validate(
        \ReflectionClass $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void {
        $constructor = $destinationReflectionClass->getConstructor();

        if (null === $constructor) {
            throw new InvalidArgumentException(
                \sprintf('Constructor Strategy is set to Always, but "%s" has no constructor or it\'s private.',
                         $destinationReflectionClass->getName()
                )
            );
        }

        if ($constructor->getNumberOfParameters() > \count($sourceMapMetadataCollection)) {
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