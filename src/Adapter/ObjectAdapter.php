<?php
declare(strict_types=1);

namespace ControlBit\Dto\Adapter;

use ControlBit\Dto\Contract\Mapper\MapAdapterInterface;

final class ObjectAdapter implements MapAdapterInterface
{
    /**
     * @param  object               $source
     * @param  object|class-string  $destination
     */
    public function adapt(mixed &$source, mixed &$destination): bool
    {
        if (!$this->supports($source, $destination)) {
            return false;
        }

        $destination = \is_object($destination)
            ? $destination
            : (new \ReflectionClass($destination))->newInstanceWithoutConstructor();

        return true;
    }

    private function supports(mixed $source, mixed $destination): bool
    {
        if (!\is_object($source)) {
            return false;
        }

        if (\is_object($destination)) {
            return true;
        }

        if (\is_string($destination) && \class_exists($destination)) {
            return true;
        }

        return false;
    }
}