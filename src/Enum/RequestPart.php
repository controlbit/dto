<?php
declare(strict_types=1);

namespace ControlBit\Dto\Enum;

/**
 * Enum for possible strategies
 */
enum RequestPart: string
{
    /**
     * Values from Query
     */
    case QUERY = 'QUERY';

    /**
     * Values from body (from JSON body to be precise)
     */
    case BODY = 'BODY';

    /**
     * Uploaded files
     */
    case FILES = 'FILES';

    public static function all()
    {
        return \array_map(
            fn(string $name) => static::from($name),
            \array_column(self::cases(), 'name'),
        );
    }

    public static function allValues()
    {
        return \array_column(self::cases(), 'value');
    }
}