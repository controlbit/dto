<?php
declare(strict_types=1);

namespace ControlBit\Dto\Adapter;

use ControlBit\Dto\Contract\CaseTransformerInterface;
use ControlBit\Dto\Contract\Mapper\MapAdapterInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class RequestAdapter implements MapAdapterInterface
{
    public function __construct(private readonly CaseTransformerInterface $caseTransformer)
    {
    }

    /**
     * @param  SymfonyRequest  $source
     * @param  class-string    $destination
     */
    public function adapt(mixed &$source, mixed &$destination): bool
    {
        if (!$this->supports($source, $destination)) {
            return false;
        }

        $requestData = $source->toArray();
        $files       = $source->files;
        $source      = (object)$this->caseTransformer->transform([...$requestData, ...$files]);
        $destination = (new \ReflectionClass($destination))->newInstanceWithoutConstructor();

        return true;
    }

    private function supports(mixed $source, mixed $destination): bool
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            return false;
        }

        if (!$source instanceof SymfonyRequest) {
            return false;
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