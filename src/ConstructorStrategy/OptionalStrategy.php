<?php
declare(strict_types=1);

namespace ControlBit\Dto\ConstructorStrategy;

use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Exception\MissingArgumentException;
use ControlBit\Dto\Exception\MissingConstructorException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final class OptionalStrategy implements ConstructorStrategyInterface
{
    public const NAME = 'optional';

    public function __construct(
        private readonly AlwaysStrategy $alwaysStrategy,
        private readonly NeverStrategy  $neverStrategy,
        private readonly bool           $mapPrivateProperties,
    ) {
    }

    public function validate(
        \ReflectionClass      $destinationReflectionClass,
        MapMetadataCollection $sourceMapMetadataCollection,
    ): void {
        $constructor = $destinationReflectionClass->getConstructor();

        if ($this->mapPrivateProperties || null !== $constructor) {
            return;
        }

        $this->neverStrategy->validate(...func_get_args()); // @phpstan-ignore-line
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        \ReflectionClass      $reflectionClass,
    ): object {
        try {
            return $this->alwaysStrategy->create(...func_get_args()); // @phpstan-ignore-line
        } catch (MissingConstructorException|MissingArgumentException) {
            return $this->neverStrategy->create(...func_get_args()); // @phpstan-ignore-line
        }
    }
}