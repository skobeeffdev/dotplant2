<?php

namespace app\components;

use app\models\Events;

class CoreBootstrap implements \yii\base\BootstrapInterface
{
    public function bootstrap($app)
    {
        $events = Events::getHandlersAll();
        /** @var Events $event */
        foreach ($events as $event) {
            if (1 !== intval($event->is_active)) {
                continue;
            }
            foreach ($event->getAllEvents() as $eventName) {
                if (is_array($eventName)) {
                    $eventHandler = current($eventName);
                    $eventName = key($eventName);
                } else {
                    $eventHandler = 'handle';
                }

                $app->on(
                    $eventName,
                    $event->getCallback($eventHandler)
                );
            }
        }
    }
}
?>