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

    /**
     * @return RequestPart[]
     */
    public static function all(): array
    {
        return \array_map(
            static fn(string $name) => self::from($name),
            \array_column(self::cases(), 'name'),
        );
    }

    /**
     * @return array<string>
     */
    public static function allValues(): array
    {
        return \array_column(self::cases(), 'value');
    }
}