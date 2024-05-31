<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

use ControlBit\Dto\Enum\RequestPart;
use ControlBit\Dto\Exception\InvalidArgumentException;

/**
 * Explains which parts of Request are mapped into DTO
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class RequestDto
{
    private readonly array $parts;

    /**
     * @param  array<string|RequestPart>|null  $parts
     *
     * @throws InvalidArgumentException
     */
    public function __construct(?array $parts = null)
    {
        if (null !== $parts) {
            foreach ($parts as $part) {
                    RequestPart::tryFrom($part) ?? throw new InvalidArgumentException(
                    \sprintf('Invalid value "%s", possible are %s', $part, \implode(', ', RequestPart::allValues()))
                );
            }
        }

        $this->parts = ($part ??= RequestPart::all());
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function hasPart(RequestPart $part): bool
    {
        return \in_array($part, $this->parts, true);
    }
}