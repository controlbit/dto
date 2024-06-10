<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper;

use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Exception\PropertyMapException;
use ControlBit\Dto\Destination\DestinationFactory;
use ControlBit\Dto\Finder\SetterFinder;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Class\ClassMetadataFactory;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;
use ControlBit\Dto\MetaData\Map\MapMetadataFactory;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;
use ControlBit\Dto\Util\Initializer;

final readonly class Mapper implements MapperInterface
{
    public function __construct(
        private ClassMetadataFactory $objectMetadataFactory,
        private MapMetadataFactory   $mapMetadataFactory,
        private DestinationFactory   $destinationFactory,
        private ValueConverter       $valueConverter,
        private SetterFinder         $setterFinder,
    ) {
    }

    public function map(object|array $source, string|object|null $destination = null): object
    {
        // Preparing source object. It must be objected.
        $source = \is_object($source) ? $source : (object)$source;

        \assert(\is_object($source));

        // Fetching source metadata. This could be cached. But not in case of stdClass.
        $sourceMetadata = $this->objectMetadataFactory->create($source);

        // Auto initialize values that are not initialized in source object.
        Initializer::autoInitialize($source, $sourceMetadata);

        // Getting metadata for mapping
        $mapMetadata = $this->mapMetadataFactory->create($sourceMetadata);

        if (!\is_object($destination)) {
            $destination = $this->destinationFactory->create(
                $this,
                $source,
                $sourceMetadata,
                $mapMetadata,
                $destination
            );
        }

        // Fetching source metadata. This could be cached. But not in case of stdClass.
        $destinationMetadata = $this->objectMetadataFactory->create($destination);
        $destination         = $this->execute(
            $source,
            $destination,
            $sourceMetadata,
            $destinationMetadata,
            $mapMetadata
        );

        Initializer::autoInitialize($destination, $destinationMetadata);

        return $destination;
    }

    private function execute(
        object                $source,
        object                $destination,
        ClassMetadata         $sourceMetadata,
        ClassMetadata         $destinationMetadata,
        MapMetadataCollection $mapMetadataCollection,
    ): object {
        foreach ($mapMetadataCollection as $mapMetadata) {
            if (!$mapMetadata->getDestinationMember() || $mapMetadata->isMappedInConstructor()) {
                continue;
            }

            $setter = $this->setterFinder->find($destinationMetadata, $mapMetadata);

            if (null === $setter) {
                continue;
            }

            /** @var PropertyMetadata $propertyMetadata */
            $propertyMetadata = $sourceMetadata->getProperty($mapMetadata->getSourceMember());
            $value            = $propertyMetadata->getAccessor()->get($source);
            $value            = $this->valueConverter->map($this, $sourceMetadata, $setter, $mapMetadata, $value);

            try {
                $setter->set($destination, $value);
            } catch (\Throwable $e) {
                throw new PropertyMapException($propertyMetadata, $sourceMetadata, $destinationMetadata, $setter, $e);
            }
        }

        return $destination;
    }
}