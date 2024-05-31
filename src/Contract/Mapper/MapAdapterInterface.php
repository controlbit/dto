<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Mapper;

interface MapAdapterInterface
{
    public function adapt(mixed &$source, mixed &$destination): bool;
}