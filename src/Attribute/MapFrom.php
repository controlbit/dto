<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

/**
 * Used on property when we want to map property to another property.
 * Good in case when we have some backward compatibility or so.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class MapFrom
{
    public function __construct(private readonly string $member)
    {
    }

    public function getMember(): string
    {
        return $this->member;
    }
}