<?php
declare(strict_types=1);

namespace ControlBit\Dto\Exception;

use ControlBit\Dto\Contract\DtoExceptionInterface;

class EntityNotFoundException extends \Exception implements DtoExceptionInterface
{
    private string     $entityClass;
    private string|int $identifier;

    public function __construct(
        string     $entityClass,
        string|int $identifier,
        ?string    $message = null,
    ) {
        parent::__construct($message ?? \sprintf('Entity "%s::%s" not found.', $entityClass, $identifier));
        $this->entityClass = $entityClass;
        $this->identifier  = $identifier;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getIdentifier(): int|string
    {
        return $this->identifier;
    }
}