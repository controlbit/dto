<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bag;

use ControlBit\Dto\MetaData\Method\MethodMetadata;

/**
 * @implements \IteratorAggregate<MethodMetadata>
 */
final class MethodBag implements \IteratorAggregate
{
    /**
     * @var MethodMetadata[]
     */
    private array $methods = [];

    public function add(MethodMetadata $methodMetadata): self
    {
        $this->methods[] = $methodMetadata;

        return $this;
    }

    public function has(string $methodName): bool
    {
        foreach ($this->methods as $propertyMetaData) {
            if ($propertyMetaData->getName() === $methodName) {
                return true;
            }
        }

        return false;
    }

    public function get(string $methodName): ?MethodMetadata
    {
        foreach ($this->methods as $methodMetadata) {
            if ($methodMetadata->getName() === $methodName) {
                return $methodMetadata;
            }
        }

        return null;
    }

    /**
     * @return \Traversable<MethodMetadata>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->methods;
    }
}