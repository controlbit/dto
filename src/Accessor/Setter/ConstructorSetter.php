<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Setter;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Exception\LogicException;

/**
 * Constructor Setter.
 */
final readonly class ConstructorSetter implements SetterInterface
{
    public function __construct(
        private TypeBag      $type,
        private AttributeBag $attributes
    ) {
    }

    public function set(object $object, mixed $value): void
    {
        throw new LogicException('This method should not be called.');
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }
}