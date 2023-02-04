<?php

declare(strict_types=1);

namespace DcrSwoole\Engine\Exception;

use App\Exception\RuntimeException;

class RunningInNonCoroutineException extends RuntimeException
{
}
