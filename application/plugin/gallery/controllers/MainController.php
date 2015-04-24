<?php

namespace app\plugin\gallery\controllers;

use yii\web\Controller;

class MainController extends Controller
{
    public function actionIndex()
    {
        echo $this->render('index', []);
    }

    public function actionEdit()
    {
        echo $this->renderContent(get_called_class() . ' : edit action');
    }
}
?>