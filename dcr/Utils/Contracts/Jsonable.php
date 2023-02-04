<?php

declare(strict_types=1);

namespace DcrSwoole\Utils\Contracts;

interface Jsonable
{
    public function __toString(): string;
}
