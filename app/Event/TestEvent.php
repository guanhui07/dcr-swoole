<?php

declare(strict_types=1);

namespace App\Event;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventManager;

/**
 * Class TestEvent
 * @package app\Event
 * @see  https://github.com/inhere/php-event-manager
 */
final class TestEvent
{
    public const preFoo = 'preFoo';
    public const postFoo = 'postFoo';

    /** @var EventManager */
    private EventManager $eventManager;

    /** @var bool */
    public bool $preFooInvoked = false;

    /** @var bool */
    public $postFooInvoked = false;

    public function __construct(EventManager $eventManager)
    {
        $eventManager->addEventListener([self::preFoo, self::postFoo], $this);
    }

    public function preFoo(EventArgs $eventArgs): void
    {
        var_dump($eventArgs);
        echo 111;
        echo '<br />';
        $this->preFooInvoked = true;
    }

    public function postFoo(EventArgs $eventArgs): void
    {
        echo 222;
        $this->postFooInvoked = true;
    }
}
