<?php

declare(strict_types=1);

namespace ControlBit\Dto\Attribute;

use ControlBit\Dto\Enum\RequestPart;
use ControlBit\Dto\Exception\InvalidArgumentException;

/**
 * Explains which parts of Request are mapped into DTO
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class RequestDto
{
    /**
     * @var RequestPart[]
     */
    private array $parts;

    /**
     * @param  array<string|RequestPart>  $parts
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $parts = [])
    {
        foreach ($parts as &$part) {
            if ($part instanceof RequestPart) {
                continue;
            }

            if (!\is_string($part) || null === RequestPart::tryFrom($part)) {
                throw new InvalidArgumentException(
                    \sprintf('Invalid value for Request DTO parts. Possible are %s',
                             \implode(', ', RequestPart::allValues()))
                );
            }

            $part = RequestPart::from($part);
        }
        unset($part);

        /** @var RequestPart[] $parts */
        $this->parts = empty($parts) ? RequestPart::all() : $parts;
    }

    /**
     * @return RequestPart[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public function hasPart(RequestPart $part): bool
    {
        return \in_array($part, $this->parts, true);
    }
}