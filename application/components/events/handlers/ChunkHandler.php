<?php

namespace app\components\events\handlers;

use app\components\events\AbstractEvent;
use app\components\events\AbstractHandler;
use app\components\events\ControllerPostRenderEvent;
use app\components\events\ControllerPreRenderEvent;

class ChunkHandler extends AbstractHandler
{
    protected $events = [
        [AbstractEvent::EVENT_CONTROLLER_BEFORE_RENDER => 'runPreRender'],
        [AbstractEvent::EVENT_CONTROLLER_AFTER_RENDER => 'runPostRender'],
    ];

    public function handle(AbstractEvent $event)
    {
    }

    public function runPreRender(ControllerPreRenderEvent $event)
    {
        $data = $event->getEventParam();

        if (!empty($data['model'])) {
            $model = $data['model'];
            $model->price = 865.666;
        }

        $event->setEventParam($data);
    }

    public function runPostRender(ControllerPostRenderEvent $event)
    {
        $content = $event->getEventParam();

        $content = preg_replace_callback(
            '#\[\[([a-zA-Z0-9\-_]+)\]\]#sU',
            function($match)
            {
                return ' Chunk '.$match[1].' were here. ';
            },
            $content);

        $event->setEventParam($content);
    }
}
?>