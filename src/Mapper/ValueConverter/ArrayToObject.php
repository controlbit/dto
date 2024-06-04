<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Mapper\Mapper;

/**
 * Usual case when we have Array on one side, and we need to map to object props on another
 */
final class ArrayToObject implements ValueConverterInterface
{
    public function supports(SetterInterface $setter, mixed $value): bool
    {
        if (\is_object($value) || !\is_iterable($value)) {
            return false;
        }

        return $setter->getType()->hasOnlyOneClass();
    }

    /**
     * @param  array<mixed>  $value
     */
    public function execute(Mapper $mapper, SetterInterface $setter, mixed $value): mixed
    {
        return $mapper->map($value, $setter->getType()->getOneClass());
    }
}