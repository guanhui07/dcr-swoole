<?php

declare(strict_types=1);

namespace App\Service\Entity;

use App\Utils\SplBean;

class TestEntity extends SplBean
{
    /**
     * @var ExchGiftInfo
     */
    public ExchGiftInfo $gift;

    public string $msg;

    public int $user_id;
}
