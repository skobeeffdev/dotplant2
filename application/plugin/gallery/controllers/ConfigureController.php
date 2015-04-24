<?php

namespace app\plugin\gallery\controllers;

use app\modules\plugin\components\BackendController;

class ConfigureController extends BackendController
{
    public function actionIndex()
    {
        return $this->renderContent('We can configure plugins.');
    }
}
?>