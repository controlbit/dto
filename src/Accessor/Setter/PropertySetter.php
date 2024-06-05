<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Setter;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

/**
 * Member setter by property name directly
 */
readonly class PropertySetter implements SetterInterface, TransformableInterface
{
    public function __construct(
        protected string       $propName,
        protected TypeBag      $type,
        protected AttributeBag $attributes,
    ) {
    }

    public function set(object $object, mixed $value): void
    {
        $object->{$this->propName} = $value;
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransformerClassOrId(): ?string
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute instanceof Transformer) {
                return $attribute->getTransformerIdOrClass();
            }
        }

        return null;
    }

    public function hasTransformer(): bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute instanceof Transformer) {
                return true;
            }
        }

        return false;
    }
}