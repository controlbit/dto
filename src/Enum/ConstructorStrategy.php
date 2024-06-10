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
     * Properties not suitable for mapping via constructor will be ignored/errored
     */
    case ALWAYS = AlwaysStrategy::NAME;

    /**
     * Will never use constructor to map object, even if it's possible
     */
    case NEVER = NeverStrategy::NAME;

    /**
     * Will use constructor if possible, otherwise will find another way to map
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