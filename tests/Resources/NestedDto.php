<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Resources;

use ControlBit\Dto\Attribute\Dto;

final class NestedDto
{
    #[Dto(NestedDto::class)]
    public array $nestedDtoArray;

    public array $scalarArray;

    public ?string $scalar;

    public ?NestedDto $nestedDto;

    public function __construct(
        #[Dto(NestedDto::class)]
        array     $nestedDtoArray = [],
        array     $scalarArray = [],
        ?string   $scalar = null,
        NestedDto $nestedDto = null,
    ) {
        $this->nestedDtoArray = $nestedDtoArray;
        $this->scalarArray    = $scalarArray;
        $this->scalar         = $scalar;
        $this->nestedDto      = $nestedDto;
    }

    public function getNestedDtoArray(): array
    {
        return $this->nestedDtoArray;
    }

    public function getScalarArray(): array
    {
        return $this->scalarArray;
    }

    public function getScalar(): string
    {
        return $this->scalar;
    }

    public function getNestedDto(): ?NestedDto
    {
        return $this->nestedDto;
    }
}