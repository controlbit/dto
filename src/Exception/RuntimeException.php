<?php
declare(strict_types=1);

namespace ControlBit\Dto\Exception;

use ControlBit\Dto\Contract\DtoExceptionInterface;

class RuntimeException extends \RuntimeException implements DtoExceptionInterface
{

}