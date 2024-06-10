<?php
declare(strict_types=1);

namespace ControlBit\Dto\Destination;

use ControlBit\Dto\Accessor\Setter\ConstructorSetter;
use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\ConstructorStrategy\StrategyCollection;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\ConstructorStrategyInterface;
use ControlBit\Dto\Contract\DestinationFactoryInterface;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final readonly class ConstructedDelegate implements DestinationFactoryInterface
{
    public function __construct(
        private ValueConverter     $valueConverter,
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

        if ($constructorStrategy->getName() === ConstructorStrategy::NEVER->value) {
            return null;
        }

        if (!$constructorStrategy->shouldInjectViaConstructor()) {
            return null;
        }

        $constructorStrategy->validate($destinationReflection, $sourceMapMetadataCollection);

        return $this->mapViaConstructor(
            $mapper,
            $source,
            $sourceClassMetadata,
            $sourceMapMetadataCollection,
            $destinationReflection,
        );
    }

    /**
     * @param  \ReflectionClass<object>  $reflectionClass
     */
    private function mapViaConstructor(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        \ReflectionClass      $reflectionClass,
    ): ?object {
        $constructor = $reflectionClass->getConstructor();

        if (null === $constructor) {
            return null;
        }

        $availableArguments = $constructor->getParameters();
        $argumentsToPass    = [];

        foreach ($availableArguments as $argument) {
            $sourceMemberMetadata = $sourceMapMetadataCollection->getHavingDestinationMember($argument->getName());

            if (null === $sourceMemberMetadata) {
                $argumentsToPass[] = $this->getArgumentValue(null, $argument);
                continue;
            }

            $sourceMemberMetadata->setMappedInConstructor();
            $propertyMetadata = $sourceMetadata->getProperty($sourceMemberMetadata->getSourceMember());

            /** @var GetterInterface $getter */
            $getter = $propertyMetadata?->getAccessor()->getGetter();
            $setter = new ConstructorSetter(
                new TypeBag(TypeTool::getReflectionTypes($argument)),
                AttributeBag::fromArray(instantiate_attributes($argument)),
            );

            $value = $this->valueConverter->map(
                $mapper,
                $sourceMetadata,
                $setter,
                $sourceMemberMetadata,
                $getter->get($source),
            );

            $argumentsToPass[] = $this->getArgumentValue($value, $argument);
        }

        return $reflectionClass->newInstanceArgs($argumentsToPass);
    }

    private function getArgumentValue(mixed $value, \ReflectionParameter $argument): mixed
    {
        if ($value !== null) {
            return $value;
        }

        if ($argument->allowsNull()) {
            return null;
        }

        if (!$argument->isDefaultValueAvailable()) {
            throw new InvalidArgumentException(
                'Tried to put value via Constructor, but different type.'
            );
        }

        return $argument->getDefaultValue();
    }

    private function getConstructorStrategy(Dto|null $dtoAttribute): ConstructorStrategyInterface
    {
        if (null === $dtoAttribute) {
            return $this->constructorStrategyCollection->getDefaultStrategy();
        }

        return $this->constructorStrategyCollection->getStrategy($dtoAttribute->getConstructorStrategy());
    }
}