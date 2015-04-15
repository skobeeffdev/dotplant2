<?php

namespace app\components\events;

use yii\base\Controller;
use yii\base\Event;
use yii\web\Application;

abstract class AbstractEvent extends Event
{
    protected $eventParam = null;

    /**
     * Special events, throw only base Event
     */
    const EVENT_SYSTEM_BEFORE_REQUEST = Application::EVENT_BEFORE_REQUEST;
    const EVENT_SYSTEM_AFTER_REQUEST = Application::EVENT_AFTER_REQUEST;

    /**
     * Special events, throw only base ActionEvent
     */
    const EVENT_CONTROLLER_BEFORE_ACTION = Controller::EVENT_BEFORE_ACTION;
    const EVENT_CONTROLLER_AFTER_ACTION = Controller::EVENT_AFTER_ACTION;

    /**
     * Application based events
     */
    const EVENT_CONTROLLER_BEFORE_RENDER = 'eventControllerBeforeRender';
    const EVENT_CONTROLLER_AFTER_RENDER = 'eventControllerAfterRender';

    public function setEventParam($param)
    {
        $this->eventParam = $param;
    }

    public function getEventParam()
    {
        return $this->eventParam;
    }

    static public function getEventConstants()
    {
        return (new \ReflectionClass(get_class()))->getConstants();
    }
}
?>