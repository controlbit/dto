<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Accessor;

use ControlBit\Dto\Contract\AttributedInterface;

interface GetterInterface extends AttributedInterface
{
    public function get(object $object): mixed;
}