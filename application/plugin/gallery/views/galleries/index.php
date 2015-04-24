<?php
    /**
     * @var \app\plugin\gallery\models\PluginGalleryGalleries[] $galleries
     * @var \yii\data\Pagination $pages
     */

    foreach ($galleries as $gallery) {
        echo '<div>'.$gallery->name.'</div>';
    }

    echo \app\widgets\LinkPager::widget(['pagination' => $pages]);
?>
<?=
    \app\modules\plugin\components\PluginWidget::draw('gallery:gallery');
?>

