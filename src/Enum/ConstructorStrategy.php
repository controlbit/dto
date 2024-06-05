<?php
declare(strict_types=1);

namespace ControlBit\Dto\Enum;

/**
 * Enum for possible strategies
 */
enum ConstructorStrategy
{
    /**
     * Will always use constructor.
     * Properties not suitable for mapping via constructor will be ignored/errored
     */
    case ALWAYS;

    /**
     * Will never use constructor to map object, even if it's possible
     */
    case NEVER;

    /**
     * Will use constructor if possible, otherwise will find another way to map
     */
    case OPTIONAL;
}