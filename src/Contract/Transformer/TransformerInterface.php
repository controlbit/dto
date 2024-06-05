<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Transformer;

interface TransformerInterface
{
    /**
     * Used when transforming from DTO to DESTINATION-OBJECT
     */
    public function transform(mixed $value): mixed;

    /**
     * Used when transforming from DOCTRINE-ENTITY to DTO
     */
    public function reverse(mixed $value): mixed;
}