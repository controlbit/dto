<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Resources;

final class DtoWithConstructor
{
    public string $foo;
    public ?string $bar;

    public function __construct(string $foo, ?string $bar = null)
    {
        $this->foo = $foo . $foo;
        $this->bar = $bar;
    }
}