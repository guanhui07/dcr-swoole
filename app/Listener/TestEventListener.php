<?php

declare(strict_types=1);

namespace App\Listener;

use Doctrine\Common\EventSubscriber;

/**
 * 事件
 * @see  https://www.doctrine-project.org/projects/doctrine-event-manager/en/latest/reference/index.html#setup
 */
final class TestEventListener implements EventSubscriber
{
    /** @var bool */
    public bool $preFooInvoked = false;

    public function preFoo(): void
    {
        $this->preFooInvoked = true;
    }

    public function getSubscribedEvents(): array
    {
        return [];
        //        return [TestEvent::preFoo];
    }
}
