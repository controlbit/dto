<?php
declare(strict_types=1);

namespace ControlBit\Dto\Util;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
final readonly class TypeTool
{
    private function __construct()
    {
        // noop
    }

    /**
     * @param  \ReflectionMethod|\ReflectionProperty|\ReflectionParameter  $reflection  $reflection
     *
     * @return array<string|null>
     */
    public static function getReflectionTypes(mixed $reflection): array
    {
        $type = null;

        if ($reflection instanceof \ReflectionProperty || $reflection instanceof \ReflectionParameter) {
            $type = $reflection->getType();
        }

        if ($reflection instanceof \ReflectionMethod) {
            $type = $reflection->getReturnType();
        }

        if (null === $type) {
            return [$type];
        }

        if ($type instanceof \ReflectionUnionType) {
            if ($type->allowsNull()) {
                return [...$type->getTypes(), null]; // @phpstan-ignore-line
            }

            return $type->getTypes(); // @phpstan-ignore-line
        }

        if ($type instanceof \ReflectionNamedType) {
            if ($type->allowsNull()) {
                return [$type->getName(), null];
            }

            return [$type->getName()];
        }

        return [null];
    }
}