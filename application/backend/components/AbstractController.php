<?php

namespace app\backend\components;

use app\backend\actions\PropertyHandler;
use app\backend\actions\UpdateEditable;
use yii\filters\AccessControl;

class AbstractController extends \yii\web\Controller
{
    protected $aclRules = ['allow' => true, 'roles' => ['product manage']];
    protected $mainModel = null;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [$this->aclRules],
            ],
        ];
    }

    public function actions()
    {
        return [
            'update-editable' => [
                'class' => UpdateEditable::className(),
                'modelName' => $this->mainModel,
                'allowedAttributes' => [],
            ],
            'property-handler' => [
                'class' => PropertyHandler::className(),
                'modelName' => $this->mainModel
            ]
        ];
    }
}
?>