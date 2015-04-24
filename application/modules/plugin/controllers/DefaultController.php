<?php

namespace app\modules\plugin\controllers;

use app\models\Plugins;
use app\modules\plugin\components\AbstractPlugin;
use app\modules\plugin\PluginModule;
use yii;
use app\backend\components\AbstractController;

class DefaultController extends AbstractController
{
    /** @var PluginModule $module */
    public $module;

    /**
     * @return array
     */
    private function refreshList()
    {
        $result = [];

        $plugins = [];
        $dirIterator = new \FilesystemIterator(Yii::getAlias('@app') . '/plugin/');
        /** @var $fInfo \SplFileInfo */
        foreach ($dirIterator as $filePath => $fInfo) {
            if ($fInfo->isDir()) {
                $_plugin = $fInfo->getRealPath() . '/' . ucfirst(strtolower($fInfo->getFilename())) . 'Plugin.php';
                if (is_file($_plugin)) {
                    $plugins[] = $_plugin;
                }
            }
        }

        foreach ($plugins as $plugin) {
            $content = file_get_contents($plugin);
            if (false === $content) {
                continue;
            }

            $tokens = token_get_all($content);

            $fileInfo = [
                'namespace' => '\\',
                'class' => '',
            ];

            $is_namespace = false;
            $is_classname = false;
            foreach ($tokens as $token) {
                if (true === $is_namespace) {
                    if (is_array($token) && in_array($token[0], [T_NS_SEPARATOR, T_STRING])) {
                        $fileInfo['namespace'] .= $token[1];
                    } elseif (in_array($token, [';', '{'])) {
                        $is_namespace = null;
                    }
                    continue;
                } elseif (true === $is_classname) {
                    if (is_array($token) && T_STRING === $token[0]) {
                        $fileInfo['class'] = $token[1];
                    } elseif (is_array($token) && T_EXTENDS === $token[0]) {
                        $is_classname = null;
                        break;
                    }
                    continue;
                }

                if (is_array($token)) {
                    if (false === $is_namespace && T_NAMESPACE === $token[0]) {
                        $is_namespace = true;
                        continue;
                    } elseif (false === $is_classname && T_CLASS === $token[0]) {
                        $is_classname = true;
                        continue;
                    }
                }
            }

            $fileInfo['namespace'] = ltrim($fileInfo['namespace'], '\\');
            $result[] = $fileInfo;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $cacheKey = static::className() . '-CacheKey';
        if (false === $cache = Yii::$app->syscache->get($cacheKey)) {
            $cache = array_filter(
                $this->refreshList(),
                function($input) {
                    if (is_subclass_of($input['namespace'].'\\'.$input['class'], '\app\modules\plugin\components\AbstractPlugin')) {
                        return true;
                    }
                    return false;
                }
            );

            if (!empty($cache)) {
                Plugins::updateAll(['is_deleted' => 1]);
                foreach ($cache as $row) {
                    /** @var AbstractPlugin $pluginClass */
                    $pluginClass = $row['namespace'].'\\'.$row['class'];
                    $pluginModel = Plugins::findOne(['class' => $pluginClass]);
                    if (empty($pluginModel)) {
                        $pluginModel = new Plugins();
                            $pluginModel->class = $pluginClass;
                            $pluginModel->name = $pluginClass::getPluginName();
                            $pluginModel->description = $pluginClass::getPluginDescription();
                    }
                    $pluginModel->is_deleted = 0;
                    $pluginModel->save();
                }

                Yii::$app->syscache->set(
                    $cacheKey,
                    $cache,
                    0,
                    new yii\caching\TagDependency([
                        'tags' => [
                            \devgroup\TagDependencyHelper\ActiveRecordHelper::getCommonTag(Plugins::className())
                        ]
                    ])
                );
            }
        }

        $models = Plugins::getPluginsAll();
        $dataProvider = new yii\data\ActiveDataProvider([
            'query' => Plugins::getPluginsAll(false),
            'pagination' => [
                'pageSize' => 50,
            ]
        ]);

        return $this->render('index',
            [
                'eventsList' => $models,
                'dataProvider' => $dataProvider
            ]
        );
    }

    /**
     * @return yii\web\Response
     */
    public function actionRefresh()
    {
        yii\caching\TagDependency::invalidate(Yii::$app->syscache, [\devgroup\TagDependencyHelper\ActiveRecordHelper::getCommonTag(Plugins::className())]);
        return $this->redirect(yii\helpers\Url::to(['index']));
    }

    /**
     * @param null $id
     * @return yii\web\Response
     * @throws yii\web\ServerErrorHttpException
     */
    public function actionInstall($id = null)
    {
        if (null === $id) {
            throw new yii\web\ServerErrorHttpException();
        }

        /** @var Plugins $pluginModel */
        $pluginModel = Plugins::findOne(['id' => $id]);
        if (empty($pluginModel)) {
            throw new yii\web\ServerErrorHttpException();
        }
        $pluginClass = $pluginModel->class;

        /** @var AbstractPlugin $pluginClass */
        if (class_exists($pluginClass)) {
            if (1 === $pluginModel->is_installed) {
                if ($this->module->uninstallPlugin($pluginClass)) {
                    $pluginModel->is_active = 0;
                    $pluginModel->is_installed = 0;
                    $pluginModel->save();
                    Yii::$app->session->setFlash('success', 'Plugin ' . $pluginClass::getPluginName() . ' uninstalled successful.');
                } else {
                    Yii::$app->session->setFlash('error', 'Errors occurred while uninstalling plugin ' . $pluginClass::getPluginName());
                }
            } else {
                if ($this->module->installPlugin($pluginClass)) {
                    $pluginModel->is_installed = 1;
                    $pluginModel->save();
                    Yii::$app->session->setFlash('success', 'Plugin ' . $pluginClass::getPluginName() . ' installed successful.');
                } else {
                    Yii::$app->session->setFlash('error', 'Errors occurred while installing plugin ' . $pluginClass::getPluginName());
                }
            }
        }

        return $this->redirect(yii\helpers\Url::to(['index']));
    }

    /**
     * @param null $id
     * @return string
     * @throws yii\web\ServerErrorHttpException
     */
    public function actionActive($id = null)
    {
        if (null === $id) {
            throw new yii\web\ServerErrorHttpException();
        }

        /** @var Plugins $pluginModel */
        $pluginModel = Plugins::findOne(['id' => $id]);
        if (empty($pluginModel)) {
            throw new yii\web\ServerErrorHttpException();
        }
        $pluginClass = $pluginModel->class;

        if (class_exists($pluginClass)) {
            if (1 === $pluginModel->is_active) {
                if ($this->module->deactivatePlugin($pluginClass)) {
                    $pluginModel->is_active = 0;
                    $pluginModel->save();
                }
            } else {
                if ($this->module->activatePlugin($pluginClass)) {
                    $pluginModel->is_active = 1;
                    $pluginModel->save();
                }
            }
        }

        return $this->redirect(yii\helpers\Url::to(['index']));
    }

    public function actionDebug()
    {
        echo yii\helpers\StringHelper::dirname(get_called_class()); exit;
    }
}
?>