<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Bag\AttributeBag;

interface AttributedInterface
{
    public function getAttributes(): AttributeBag;
}