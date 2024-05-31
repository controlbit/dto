<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Mapper;

interface MapperInterface
{
    public function map(object|array $source, ?string $destination = null): object;
}