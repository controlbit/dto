<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bridge\Symfony\Event;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Attribute\RequestDto;
use ControlBit\Dto\Contract\CaseTransformerInterface;
use ControlBit\Dto\Enum\RequestPart;
use ControlBit\Dto\Exception\ValidationException;
use ControlBit\Dto\Mapper\Mapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function ControlBit\Dto\find_attribute;

readonly class DtoArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private Mapper                   $mapper,
        private CaseTransformerInterface $caseTransformer,
        private ?ValidatorInterface      $validator,
    ) {
    }

    /**
     * @return iterable<object>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->shouldResolve($argument)) {
            return [];
        }

        $dto = $this->mapper->map($this->requestToArray($request, $argument), $argument->getType());

        $this->validate($dto);

        return [$dto];
    }

    private function shouldResolve(ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();

        if (null === $type || !\class_exists($type)) {
            return false;
        }

        if (null === find_attribute($type,
                                    Dto::class) && \count($argument->getAttributesOfType(RequestDto::class)) === 0) {
            return false;
        }

        return true;
    }

    private function validate(object $dto): void
    {
        if (null === $this->validator) {
            return;
        }

        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }

    /**
     * @return array<mixed>
     */
    private function requestToArray(Request $request, ArgumentMetadata $argument): array
    {
        $requestDto = $argument->getAttributesOfType(RequestDto::class)[0] ?? new RequestDto();;

        try {
            $requestData = $requestDto->hasPart(RequestPart::BODY) ? $request->toArray() : [];
        } catch (\Exception) {
            $requestData = [];
        }

        $files       = $requestDto->hasPart(RequestPart::FILES) ? $request->files : [];
        $queryParams = $requestDto->hasPart(RequestPart::QUERY) ? $request->query : [];

        return $this->caseTransformer->transform([...$requestData, ...$files, ...$queryParams]);
    }
}