<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Accessor;

use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\AttributedInterface;

interface SetterInterface extends AttributedInterface
{
    public function set(object $object, mixed $value): void;

    public function getType(): TypeBag;
}