<?php

namespace app\modules\plugin\components;

class BackendController extends \app\backend\components\AbstractController
{
    public function init()
    {
        parent::init();
        $this->layout = $this->module->getBackendLayout();
        \Yii::$app->params['backendPath'] = true;
    }
}
?>