<?php

declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class SampleEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column]
    private int $count;

    public function __construct(?string $id = null, int $count = 0)
    {
        $this->id    = null !== $id ? Uuid::fromString($id) : null;
        $this->count = $count;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): SampleEntity
    {
        $this->count = $count;

        return $this;
    }
}