<?php
declare(strict_types=1);

namespace ControlBit\Dto\Factory;

use ControlBit\Dto\Accessor\Setter\ConstructorSetter;
use ControlBit\Dto\Attribute\Entity;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Exception\EntityNotFoundException;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\RuntimeException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use ControlBit\Dto\Util\TypeTool;
use Doctrine\Persistence\ManagerRegistry;
use function ControlBit\Dto\instantiate_attributes;

final class DestinationFactory
{
    public function __construct(
        private readonly ValueConverter      $valueConverter,
        private readonly ConstructorStrategy $constructorStrategy = ConstructorStrategy::OPTIONAL,
        private readonly ?ManagerRegistry    $doctrineRegistry = null,
    ) {
    }

    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        ?string               $destination,
    ): object {
        /** @var ?Entity $entityAttribute */
        $entityAttribute = $sourceClassMetadata->getAttributes()->get(Entity::class);

        /** @var string|int|null $identifier */
        $identifier = $sourceClassMetadata->getIdentifierProperty()?->getAccessor()->get($source);

        if (null === $destination && null === $entityAttribute) {
            throw new InvalidArgumentException(
                \sprintf('Neither destination is set or source have %s attribute', Entity::class)
            );
        }

        if (null !== $destination && !\class_exists($destination)) {
            throw new InvalidArgumentException(
                \sprintf('There is not such class: "%s"', $destination)
            );
        }

        if (null !== $entityAttribute?->getTarget() && null !== $identifier) {
            return $this->fetchEntity($entityAttribute->getTarget(), $identifier);
        }

        $reflectionClass = new \ReflectionClass($destination ?? $entityAttribute->getTarget());

        if ($this->constructorStrategy === ConstructorStrategy::NEVER) {
            return $reflectionClass->newInstanceWithoutConstructor();
        }

        $constructor = $reflectionClass->getConstructor();

        if ($this->constructorStrategy === ConstructorStrategy::ALWAYS && null === $constructor) {
            throw new InvalidArgumentException(
                \sprintf('Constructor Strategy is set to Always, but "%s" has no constructor or it\'s private.', $destination)
            );
        }

        if ($this->constructorStrategy === ConstructorStrategy::ALWAYS && $constructor?->getNumberOfParameters() ?? 0 > \count($sourceMapMetadataCollection)) {
            throw new InvalidArgumentException(
                'Not enough arguments in constructor, to be able to map with ConstructorStrategy "Always".'
            );
        }

        if (null === $constructor) {
            return $reflectionClass->newInstanceWithoutConstructor();
        }

        return $this->mapViaConstructor(
            $mapper,
            $source,
            $sourceClassMetadata,
            $sourceMapMetadataCollection,
            $reflectionClass,
        );
    }

    private function mapViaConstructor(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        \ReflectionClass      $reflectionClass,
    ): object {
        $constructor = $reflectionClass->getConstructor();

        /** @var \ReflectionParameter[] $availableArguments */
        $availableArguments = $constructor?->getParameters() ?? [];
        $argumentsToPass    = [];

        foreach ($availableArguments as $argument) {
            $sourceMemberMetadata = $sourceMapMetadataCollection->getHavingDestinationMember($argument->getName());

            if (null === $sourceMemberMetadata) {
                if ($argument->allowsNull()) {
                    $argumentsToPass[] = null;
                    continue;
                }

                if (!$argument->isDefaultValueAvailable()) {
                    throw new InvalidArgumentException(
                        \sprintf('Unable to map argument "%s".', $argument->getName())
                    );
                }

                $argumentsToPass[] = $argument->getDefaultValue();

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

            if (null === $value && !$argument->allowsNull()) {
                if (!$argument->isDefaultValueAvailable()) {
                    throw new InvalidArgumentException(
                        'Tried to put value via Constructor, but different type.'
                    );
                }

                $value = $argument->getDefaultValue();
            }

            $argumentsToPass[] = $value;
        }

        return $reflectionClass->newInstanceArgs($argumentsToPass);
    }

    private function fetchEntity(string $destination, string|int $identifier): object
    {
        $entityManager = $this->doctrineRegistry->getManagerForClass($destination);

        if (null === $entityManager) {
            throw new RuntimeException(
                \sprintf('Entity manager not found for entity "%s".', $destination)
            );
        }

        $entity = $entityManager->find($destination, $identifier);

        if (null === $entity) {
            throw new EntityNotFoundException($destination, $identifier);
        }

        return $entity;
    }
}