<?php
declare(strict_types=1);

namespace ControlBit\Dto\Destination;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Contract\DestinationFactoryInterface;
use ControlBit\Dto\Exception\EntityNotFoundException;
use ControlBit\Dto\Exception\RuntimeException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use Doctrine\Persistence\ManagerRegistry;

final readonly class EntityDelegate implements DestinationFactoryInterface
{
    public function __construct(private ?ManagerRegistry $doctrineRegistry = null) {
    }

    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $sourceMapMetadataCollection,
        ?string               $destination,
    ): object|string|null {
        /** @var ?Dto $dtoAttribute */
        $dtoAttribute = $sourceClassMetadata->getAttributes()->get(Dto::class);
        $entityClass  = $dtoAttribute?->getEntityClass();

        if (!$this->supports($entityClass)) {
            return null;
        }

        /** @var string|int|null $identifier */
        $identifier = $sourceClassMetadata->getIdentifierProperty()?->getAccessor()->get($source);

        if (null !== $this->doctrineRegistry && null !== $identifier && null !== $entityClass) {
            return $this->fetchEntity($entityClass, $identifier);
        }

        return $entityClass;
    }

    private function supports(?string $entityClass): bool
    {
        return null !== $entityClass;
    }

    /**
     * @param  class-string  $destination
     */
    private function fetchEntity(string $destination, string|int $identifier): object
    {
        $entityManager = $this->doctrineRegistry->getManagerForClass($destination); // @phpstan-ignore-line

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