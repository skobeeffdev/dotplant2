<?php

namespace app\backend\controllers;

use app\components\events\AbstractEvent;
use app\components\events\AbstractHandler;
use app\models\Events;
use yii;
use yii\caching\TagDependency;

class EventsController extends \app\backend\components\AbstractController
{
    protected $mainModel = '\app\models\Events';

    public function actions()
    {
        $actions = parent::actions();

        $actions = yii\helpers\ArrayHelper::merge($actions, [
            'allowedAttributes' => [
                'is_active' => function(Events $model) {
                    if (1 === $model->is_active) {
                        $label_class = 'label-success';
                        $value = 'Active';
                    } else {
                        $value = 'Inactive';
                        $label_class = 'label-default';
                    }
                    return \yii\helpers\Html::tag(
                        'span',
                        Yii::t('app', $value),
                        ['class' => "label $label_class"]
                    );
                }
            ]
        ]);

        return $actions;
    }

    private function refreshEventList()
    {
        $result = [];

        $dirEvents = [
            Yii::getAlias('@webroot') . '/theme/module/components/events/',
            Yii::getAlias('@app') . '/components/events/handlers/'
        ];

        foreach ($dirEvents as $dirName) {
            $dirIterator = new \FilesystemIterator($dirName);
            foreach ($dirIterator as $filePath => $fInfo) {
                $content = file_get_contents($filePath);
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

                $result[] = $fileInfo;
            }
        }

        return $result;
    }

    public function actionIndex()
    {
        $cacheKey = 'ThemeEventFileList';
        $cache = Yii::$app->syscache->get($cacheKey);
        if (false === $cache) {
            $cache = array_filter(
                $this->refreshEventList(),
                function($input) {
                    if (is_subclass_of($input['namespace'].'\\'.$input['class'], '\app\components\events\AbstractHandler')) {
                        return true;
                    }
                    return false;
                }
            );

            if (!empty($cache)) {
                Events::updateAll(['is_deleted' => 1]);
                foreach ($cache as $row) {
                    $handlerClass = $row['namespace'].'\\'.$row['class'];
                    /** @var AbstractHandler $handler */
                    $handler = new $handlerClass();
                    Events::createHandler($handler->getConfig());
                }

                Yii::$app->syscache->set(
                    $cacheKey,
                    $cache,
                    0,
                    new TagDependency([
                        'tags' => [
                            \devgroup\TagDependencyHelper\ActiveRecordHelper::getCommonTag(Events::className())
                        ]
                    ])
                );
            }
        }

        $models = Events::getHandlersAll();
        $dataProvider = new yii\data\ActiveDataProvider([
            'query' => Events::getHandlersAll(false),
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

    public function actionRefresh()
    {
        TagDependency::invalidate(Yii::$app->syscache, [\devgroup\TagDependencyHelper\ActiveRecordHelper::getCommonTag(Events::className())]);
        return $this->redirect(yii\helpers\Url::to(['index']));
    }

    public function actionEdit($id = null)
    {
        if (null === $id ) {
            throw new yii\web\ServerErrorHttpException;
        }

        return $this->render('edit', []);
    }
}
?>