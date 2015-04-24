<?php

namespace app\modules\plugin;

use app\components\UrlManager;
use app\modules\plugin\components\AbstractPlugin;
use app\modules\plugin\components\PluginWidget;
use yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module;

class PluginModule extends Module implements BootstrapInterface
{
    private $moduleConfig = [];
    private $moduleConfigDirty = false;

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->on(
            Application::EVENT_BEFORE_ACTION,
            function() use ($app)
            {
                if ($app->requestedAction->controller->module instanceof $this) {
                    $app->params['backendPath'] = true;
                }
            },
            null,
            false
        );

        /** @var AbstractPlugin $pluginClass */
        foreach ($this->moduleConfig['plugins'] as $pluginClass => $value) {
            $plugin = new $pluginClass($pluginClass::getPluginId(), $this);
            $this->setModule($plugin->id, $plugin);

            if (isset($this->moduleConfig['bootstrap'][$pluginClass])) {
                $plugin->bootstrap($app);
            }
        }

        PluginWidget::$pluginModule = $this;
        PluginWidget::$pluginWidgetMap = $this->moduleConfig['widgets'];
    }

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->layout = \Yii::$app->getModule('backend')->layout;

        $moduleConfig = \Yii::getAlias('@app/config/plugins-config.php');
        if (is_file($moduleConfig)) {
            $moduleConfig = include_once $moduleConfig;
            $moduleConfig = is_array($moduleConfig) ? $moduleConfig : [$moduleConfig];

            $this->moduleConfig = array_merge([
                'bootstrap' => [],
                'plugins' => [],
                'widgets' => [],
            ], $moduleConfig);

            $cacheKey = $this->className() . '-CacheKey';
            if (false === $cache = Yii::$app->syscache->get($cacheKey)) {

            }
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->moduleConfigDirty) {
            $moduleConfig = \Yii::getAlias('@app/config/plugins-config.php');
            $data = sprintf('<?php return %s; ?>', var_export($this->moduleConfig, true));

            $file = fopen($moduleConfig, 'w+');
            if ($file) {
                fwrite($file, $data);
                fclose($file);
            }
        }
    }

    /**
     * @param AbstractPlugin $className
     * @param bool $isRemoved
     */
    private function changePluginConfig($className, $isRemoved = false)
    {
        yii\caching\TagDependency::invalidate(
            Yii::$app->syscache,
            [
                \devgroup\TagDependencyHelper\ActiveRecordHelper::getCommonTag(static::className())
            ]
        );

        if (!$isRemoved) {
            if (is_subclass_of($className, '\yii\base\BootstrapInterface')) {
                $this->moduleConfig['bootstrap'][$className] = $className;
            }

            $this->moduleConfig['plugins'][$className] = $className;

            $this->moduleConfig['widgets'] = array_merge($this->moduleConfig['widgets'], $className::getWidgets());

            /** @var UrlManager $urlManager */
            $urlManager = Yii::$app->urlManager;
            $routes = $className::getRoutes();
            if (!empty($routes) && is_array($routes)) {
                $urlManager->addRules($routes);
                $urlManager->compileRuleCache();
            }
        } else {
            if (is_subclass_of($className, '\yii\base\BootstrapInterface')) {
                unset($this->moduleConfig['bootstrap'][$className]);
            }

            unset($this->moduleConfig['plugins'][$className]);

            while (false !== $index = array_search($className, $this->moduleConfig['widgets'])) {
                unset($this->moduleConfig['widgets'][$index]);
            }
        }
    }

    /**
     * @param AbstractPlugin|null $className
     * @return bool
     */
    public function installPlugin($className = null)
    {
        if (empty($className)) {
            return false;
        }

        $this->moduleConfigDirty = true;

        /** @var AbstractPlugin $plugin */
        $plugin = new $className($className::getPluginId());
        $plugin->installPlugin($this);
        if (!$plugin->hasErrors()) {
            return true;
        }

        return false;
    }

    /**
     * @param AbstractPlugin|null $className
     * @return bool
     */
    public function uninstallPlugin($className = null)
    {
        if (empty($className)) {
            return false;
        }

        $this->moduleConfigDirty = true;

        /** @var AbstractPlugin $plugin */
        $plugin = new $className($className::getPluginId());
        $plugin->uninstallPlugin($this);
        if (!$plugin->hasErrors()) {
            $this->changePluginConfig($className, true);
            return true;
        }

        return false;
    }

    /**
     * @param AbstractPlugin|null $className
     * @return bool
     */
    public function activatePlugin($className = null)
    {
        if (empty($className)) {
            return false;
        }

        $this->moduleConfigDirty = true;
        $this->changePluginConfig($className);

        return true;
    }

    /**
     * @param AbstractPlugin|null $className
     * @return bool
     */
    public function deactivatePlugin($className = null)
    {
        if (empty($className)) {
            return false;
        }

        $this->moduleConfigDirty = true;
        $this->changePluginConfig($className, true);

        return true;
    }
}
?>