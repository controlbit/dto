<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bag;

use Traversable;

/**
 * @implements \IteratorAggregate<Object>
 */
final class AttributeBag implements \IteratorAggregate
{
    /**
     * @var Object[]
     */
    private array $attributes = [];

    public function add(object $attribute): self
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param  class-string  $attributeClass
     */
    public function has(string $attributeClass): bool
    {
        foreach ($this->attributes as $attribute) {
            if (\get_class($attribute) === $attributeClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * @template T of object
     * @param  class-string<T>  $attributeClass
     * @return T
     */
    public function get(string $attributeClass): ?object
    {
        foreach ($this->attributes as $attribute) {
            if (\get_class($attribute) === $attributeClass) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @param  object[]  $attributes
     */
    public static function fromArray(array $attributes): self
    {
        $self = new self();
        foreach ($attributes as $attribute) {
            $self->add($attribute);
        }

        return $self;
    }

    public function getIterator(): Traversable
    {
        yield from $this->attributes;
    }
}