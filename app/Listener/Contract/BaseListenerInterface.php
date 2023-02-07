<?php
declare(strict_types = 1);

namespace App\Listener\Contract;


interface BaseListenerInterface
{
    public function process(object $event);
}