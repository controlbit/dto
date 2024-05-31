<?php
declare(strict_types=1);

namespace ControlBit\Dto\Adapter;

use ControlBit\Dto\Contract\Mapper\MapAdapterInterface;

/**
 * Adapts Source and Destination
 * when Source is Array and destination is Given object or a class.
 */
final class ArrayAdapter implements MapAdapterInterface
{
    /**
     * @param  array<string, mixed>  $source
     * @param  class-string|object   $destination
     */
    public function adapt(mixed &$source, mixed &$destination): bool
    {
        if (!$this->supports($source, $destination)) {
            return false;
        }

        $source      = (object)$source;
        $destination = \is_object($destination)
            ? $destination
            : (new \ReflectionClass($destination))->newInstanceWithoutConstructor();

        return true;
    }

    private function supports(mixed $source, mixed $destination): bool
    {
        if (!\is_iterable($source)) {
            return false;
        }

        if (\is_object($destination)) {
            return true;
        }

        if (!\is_string($destination)) {
            return false;
        }

        if (!\class_exists($destination)) {
            return false;
        }

        return true;
    }
}