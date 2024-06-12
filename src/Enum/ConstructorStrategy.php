<?php
declare(strict_types=1);

namespace ControlBit\Dto\Enum;

use ControlBit\Dto\ConstructorStrategy\AlwaysStrategy;
use ControlBit\Dto\ConstructorStrategy\NeverStrategy;
use ControlBit\Dto\ConstructorStrategy\OptionalStrategy;

/**
 * Enum for possible strategies
 */
enum ConstructorStrategy: string
{
    /**
     * Will always use constructor.
     * If unable to find all values for Constructor, error will be thrown.
     */
    case ALWAYS = AlwaysStrategy::NAME;

    /**
     * Will never use constructor to map object.
     */
    case NEVER = NeverStrategy::NAME;

    /**
     * Will use constructor if possible, otherwise will find another way to map.
     * Also, if it finds that it cannot map all members using Constructor, and they are required,
     * it will then proceed without constructor.
     */
    case OPTIONAL = OptionalStrategy::NAME;

    /**
     * @return array<value-of<ConstructorStrategy>>
     */
    public static function all(): array
    {
        return \array_values(\array_map(static fn($case) => $case->value, self::cases()));
    }
}