<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bag;

use ControlBit\Dto\MetaData\PropertyMetadata;

/**
 * @implements \IteratorAggregate<PropertyMetadata>
 */
final class PropertyBag implements \IteratorAggregate
{
    /**
     * @var PropertyMetadata[]
     */
    private array $properties = [];

    public function add(PropertyMetadata $propertyMetadata): self
    {
        $this->properties[] = $propertyMetadata;

        return $this;
    }

    public function has(string $propertyName): bool
    {
        foreach ($this->properties as $propertyMetaData) {
            if ($propertyMetaData->getName() === $propertyName) {
                return true;
            }
        }

        return false;
    }

    public function get(string $propertyName): ?PropertyMetadata
    {
        foreach ($this->properties as $propertyMetaData) {
            if ($propertyMetaData->getName() === $propertyName) {
                return $propertyMetaData;
            }
        }

        return null;
    }

    /**
     * @return \Traversable<PropertyMetadata>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->properties;
    }
}