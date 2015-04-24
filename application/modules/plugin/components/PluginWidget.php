<?php

namespace app\modules\plugin\components;

class PluginWidget
{
    static public $pluginModule = null;
    static public $pluginWidgetMap = [];

    static public function draw($widget, $config = [])
    {
        if (empty(static::$pluginModule)) {
            return '';
        }

        if (isset(static::$pluginWidgetMap[$widget])) {
            /** @var AbstractPlugin $plugin */
            $plugin = static::$pluginWidgetMap[$widget];
            return $plugin::drawWidget(substr($widget, strrpos($widget, ':') + 1), $config);
        }

        return '';
    }
}
?>