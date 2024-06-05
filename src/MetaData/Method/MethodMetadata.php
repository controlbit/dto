<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Method;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\AttributedInterface;

final readonly class MethodMetadata implements AttributedInterface
{
    public function __construct(private string $name, private TypeBag $type, private AttributeBag $attributes)
    {
    }

    public function getName(): string
    {
        return $this->name;
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