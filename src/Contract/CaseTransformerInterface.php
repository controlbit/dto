<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

interface CaseTransformerInterface
{
    /**
     * @param  array<string, mixed>  $array
     *
     * @return array<string, mixed>
     */
    public function transform(array $array): array;
}