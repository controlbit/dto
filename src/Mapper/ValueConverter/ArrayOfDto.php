<?php
declare(strict_types=1);

namespace ControlBit\Dto\Mapper\ValueConverter;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\ValueConverterInterface;
use ControlBit\Dto\Exception\RuntimeException;
use ControlBit\Dto\Mapper\Mapper;

/**
 *  Case when we have an iterable, and property to map to is
 *  marked as array of dto by #[Dto] Attribute.
 */
final class ArrayOfDto implements ValueConverterInterface
{
    public function supports(SetterInterface $setter, mixed $value): bool
    {
        if (\is_object($value) || !\is_iterable($value)) {
            return false;
        }

        if ($setter->getAttributes()->has(Dto::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param  iterable<object>|object[]  $value
     */
    public function execute(Mapper $mapper, SetterInterface $setter, mixed $value): mixed
    {
        $class = $setter->getType()->getOneClass();

        try {
            /** @var Dto $destinationDtoAttribute */
            $destinationDtoAttribute = $setter->getAttributes()->get(Dto::class)
                                       ??
                                       /* @phpstan-ignore-next-line  */
                                       ((new \ReflectionClass($class))->getAttributes(Dto::class)[0])->newInstance();

        } catch (\ReflectionException) {
            throw new RuntimeException('Unable to determine destination DTO class.');
        }

        return \array_map(
            static fn(object|array $item) => $mapper->map($item, $destinationDtoAttribute->getClass()),
            [...$value],
        );
    }
}