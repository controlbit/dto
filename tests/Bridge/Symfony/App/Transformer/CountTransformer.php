<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;

final class CountTransformer implements TransformerInterface
{

    public function transform(mixed $value): mixed
    {
        return $value + 100;
    }

    public function reverse(mixed $value): mixed
    {
        return $value - 100;
    }
}