<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Mapper;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;

interface PropertyMapperInterface
{
    public function map(mixed $value, object $object): void;

    public function getTransformer(): ?TransformerInterface;
}