<?php
declare(strict_types = 1);

namespace App\Listener;


interface BaseListenerInterface
{
    public function process(object $event);
}