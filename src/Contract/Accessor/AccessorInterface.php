<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Accessor;

interface AccessorInterface
{
    public function set(object $object, mixed $value): void;

    public function get(object $object): mixed;

    public function getSetter(): ?SetterInterface;

    public function getGetter(): ?GetterInterface;
}