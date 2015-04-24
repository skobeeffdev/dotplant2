<?php

namespace app\backend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;

class ModuleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['media manage'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        echo $this->renderContent('YO!');
    }

    public function actionDefault($params = null)
    {
        if (null === $params) {
            throw new ServerErrorHttpException('Invalid input params.');
        }

        $request = parse_url($params);
        if (empty($request) || empty($request['path'])) {
            throw new ServerErrorHttpException('Empty request.');
        }

        $path = trim($request['path'], '/');
        $path = explode('/', $path);
        if (empty($path)) {
            throw new ServerErrorHttpException('Empty path.');
        }

        $module = array_shift($path);
        $className = '\\app\\modules\\' . strtolower($module) . '\\' . ucfirst(strtolower($module)) . 'Module';

        if (!class_exists($className)) {
            throw new ServerErrorHttpException('Module not found!');
        }

        $action = array_shift($path);
        if (empty($action)) {
            $action = 'index';
        }

        $query_params = [];
        if (!empty($request['query'])) {
            parse_str($request['query'], $query_params);
        }

        /** @var Controller $instance */
        $instance = \Yii::createObject($className, [$module, $this->module]);
            $instance->layout = $this->layout;
        return $instance->runAction($action, $query_params);
    }

}
?>