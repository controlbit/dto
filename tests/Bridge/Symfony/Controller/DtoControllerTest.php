<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\Controller;

use ControlBit\Dto\Tests\SymfonyTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DtoControllerTest extends SymfonyTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
    }

    public function testBasicResponse(): void
    {
        $this->client->jsonRequest(Request::METHOD_POST, 'dto/basic', [
            'nested_dto' => [
                'scalar_array' => [1, 2, 3],
                'scalar'       => 'foo',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertEquals(
            [
                'nestedDto'      => [
                    'scalarArray'    => [1, 2, 3],
                    'scalar'         => 'foo',
                    'nestedDtoArray' => [],
                    'nestedDto'      => null,
                ],
                'nestedDtoArray' => [],
                'scalarArray'    => [],
                'scalar'         => null,
            ],
            \json_decode((string)$this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function testAssertedDtoResponseIsValid(): void
    {
        $this->client->jsonRequest(Request::METHOD_POST, 'dto/asserted', [
            'foo' => 'bar',
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testAssertedDtoResponseIsInValidBecauseBlank(): void
    {
        $this->client->jsonRequest(Request::METHOD_POST, 'dto/asserted', [
            'foo' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertEquals(
            [
                'errors' =>
                    [
                        [
                            'message'       => 'This value should not be blank.',
                            'path'          => 'foo',
                            'invalid_value' => null,
                            'template'      => 'This value should not be blank.',
                        ],
                    ],
            ],
            \json_decode((string)$this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function testAssertedDtoResponseIsInValidBecauseTooShort(): void
    {
        $this->client->jsonRequest(Request::METHOD_POST, 'dto/asserted', [
            'foo' => 'b',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertEquals(
            [
                'errors' =>
                    [
                        [
                            'message'       => 'This value is too short. It should have 3 characters or more.',
                            'path'          => 'foo',
                            'invalid_value' => 'b',
                            'template'      => 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
                        ],
                    ],
            ],
            \json_decode((string)$this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }
}