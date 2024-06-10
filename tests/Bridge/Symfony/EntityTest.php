<?php

declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Attribute\Identifier;
use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Exception\RuntimeException;
use ControlBit\Dto\Tests\Bridge\Symfony\App\Entity\SampleEntity;
use ControlBit\Dto\Tests\Bridge\Symfony\App\Fixtures\AppFixtures;
use ControlBit\Dto\Tests\Bridge\Symfony\App\Transformer\CountTransformer;
use ControlBit\Dto\Tests\Resources\EntityWithoutManager;
use ControlBit\Dto\Tests\SymfonyTestCase;
use Doctrine\ORM\EntityManagerInterface;

class EntityTest extends SymfonyTestCase
{
    private MapperInterface $mapper;

    private EntityManagerInterface $entityManger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper       = self::getContainer()->get('dto_bundle.mapper');
        $this->entityManger = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testEntityIsFetchedAndUpdated(): void
    {
        $id = AppFixtures::$ID;

        $source = new #[Dto(entityClass: SampleEntity::class)] class($id) {
            #[Identifier]
            public string $id;

            public int $count = 30;

            public function __construct(string $id)
            {
                $this->id = $id;
            }
        };

        /** @var SampleEntity $mappedObject */
        $mappedObject = $this->mapper->map($source);
        $this->assertEquals($id, $mappedObject->getId());
        $this->assertEquals(30, $mappedObject->getCount());
    }

    public function testEntityIsNew(): void
    {
        $source = new #[Dto(entityClass: SampleEntity::class)] class() {
            public int $count = 30;
        };

        /** @var SampleEntity $mappedObject */
        $mappedObject = $this->mapper->map($source);

        $this->assertNull($mappedObject->getId());
        $this->assertEquals(30, $mappedObject->getCount());

        $this->entityManger->persist($mappedObject);
        $this->entityManger->flush($mappedObject);
    }

    public function testEntityDoesntHaveManager(): void
    {
        $source = new #[Dto(entityClass: EntityWithoutManager::class)] class() {
            #[Identifier]
            public string $id = "a99571f8-2b5a-4c0a-a7ce-fa1845c588c9"; // This should not be assigned nor found.
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf('Entity manager not found for entity "%s".', EntityWithoutManager::class)
        );

        $this->mapper->map($source);
    }

    public function testEntityDoesReverseTransformation(): void
    {
        $source      = $this->entityManger->getRepository(SampleEntity::class)->find(AppFixtures::$ID);
        $destination = new class() {
            #[Transformer(CountTransformer::class)]
            public int  $count;
        };

        $mappedObject = $this->mapper->map($source, $destination);

        self::assertEquals(-100, $mappedObject->count);
    }
}