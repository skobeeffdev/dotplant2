<?php

namespace app\plugin\gallery\controllers;

use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\Controller;
use app\plugin\gallery\models\PluginGalleryGalleries as Galleries;

class GalleriesController extends Controller
{
    public function actionIndex()
    {
        $query = Galleries::find();
        $queryCount = clone $query;
        $pages = new Pagination([
            'forcePageParam' => false,
            'totalCount' => $queryCount->count(),
        ]);
        $galleries = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        echo $this->render(
            'index',
            [
                'galleries' => $galleries,
                'pages' => $pages,
            ]
        );
    }
}
?>