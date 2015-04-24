<?php

namespace app\modules\plugin\components;

use app\models\Plugins;
use app\modules\plugin\PluginModule;
use yii\base\Module;
use yii\base\Widget;
use yii\helpers\StringHelper;

abstract class AbstractPlugin extends Module
{
    static protected $pluginId = null;
    static protected $pluginName = null;
    static protected $pluginDescription = null;
    static protected $configureRoute = null;
    static protected $routes = [];
    static protected $widgetsMap = [];

    static protected $pluginNamespace = null;

    protected $errors = null;

    abstract public function installPlugin(PluginModule $module);
    abstract public function uninstallPlugin(PluginModule $module);
    abstract public function reinstallPlugin(PluginModule $module);

    public function init()
    {
        parent::init();
        $this->id = static::getPluginId();
    }

    protected function getConfig()
    {
        /** @var Plugins $model */
        $model = Plugins::findOne(['class' => get_called_class()]);

        return empty($model) ? null : $model->options;
    }

    protected function updateConfig($config)
    {
        /** @var Plugins $model */
        if (null !== $model = Plugins::findOne(['class' => get_called_class()])) {
            $model->options = $config;
            $model->save();
        }
    }

    public function getBackendLayout()
    {
        return $this->module->layout;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    static public function getPluginName()
    {
        if(empty(static::$pluginName)) {
            static::$pluginName = StringHelper::basename(get_called_class());
        }

        return static::$pluginName;
    }

    static public function getPluginDescription()
    {
        if(empty(static::$pluginDescription)) {
            static::$pluginDescription = StringHelper::basename(get_called_class());
        }

        return static::$pluginDescription;
    }

    static public function getPluginId()
    {
        if(empty(static::$pluginId)) {
            static::$pluginId = StringHelper::basename(get_called_class());
            static::$pluginId = mb_strtolower(static::$pluginId, 'UTF-8');
        }

        return static::$pluginId;
    }

    static public function getPluginNamespace()
    {
        if (empty(static::$pluginNamespace)) {
            static::$pluginNamespace = StringHelper::dirname(get_called_class());
        }

        return static::$pluginNamespace;
    }

    static public function getConfigureUrl()
    {
        return static::$configureRoute;
    }

    static public function getRoutes()
    {
        return static::$routes;
    }

    static public function getWidgets()
    {
        $id = static::getPluginId();
        $className = get_called_class();
        return array_reduce(array_keys(static::$widgetsMap),
            function($result, $item) use ($id, $className)
            {
                $widget = $id . ':' . $item;
                $result[$widget] = $className;
                return $result;
            },
            []
        );
    }

    static public function drawWidget($name, $config = [])
    {
        if (isset(static::$widgetsMap[$name])) {
            /** @var Widget $name */
            $name = static::getPluginNamespace() . '\widgets\\' . static::$widgetsMap[$name];
            return $name::widget($config);
        }

        return '';
    }
}
?>