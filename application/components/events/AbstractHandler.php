<?php

namespace app\components\events;

abstract class AbstractHandler
{
    protected $name = null;
    protected $description = null;
    protected $events = [];

    public function __construct()
    {
        if (empty($this->name)) {
            $name = explode('\\', get_called_class());
            $this->name = array_pop($name);
        }

        if (empty($this->description)) {
            $this->description = $this->name;
        }
    }

    public function getConfig()
    {
        return [
            'class' => get_called_class(),
            'name' => $this->name,
            'description' => $this->description,
            'options' => [
                'events' => $this->events,
            ]
        ];
    }

    abstract public function handle(AbstractEvent $event);
}
?>