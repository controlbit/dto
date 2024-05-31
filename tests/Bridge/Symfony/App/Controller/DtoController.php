<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App\Controller;

use ControlBit\Dto\Attribute\RequestDto;
use ControlBit\Dto\Tests\Resources\AssertedDto;
use ControlBit\Dto\Tests\Resources\NestedDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class DtoController extends AbstractController
{
    public function basic(#[RequestDto] NestedDto $dto): Response
    {
        return $this->json($dto);
    }

    public function asserted(#[RequestDto] AssertedDto $dto): Response
    {
        return $this->json($dto);
    }
}