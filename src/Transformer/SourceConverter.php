<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\CaseTransformerInterface;
use Symfony\Component\HttpFoundation\Request;

final class SourceConverter
{
    public function __construct(private readonly CaseTransformerInterface $caseTransformer)
    {
    }

    public function convert(object|array $source): object
    {
        return match (true) {
            \is_array($source)         => $this->fromArray($source),
            $source instanceof Request => $this->fromRequest($source),
            default                    => $source
        };
    }

    private function fromArray(array $array): object
    {
        return (object)$array;
    }

    private function fromRequest(Request $request)
    {

    }
}