<?php

/**
 * @var yii\web\View $this
 */

use kartik\dynagrid\DynaGrid;
use kartik\helpers\Html;

$this->title = Yii::t('app', 'Plugins');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $this->beginBlock('refresh-button'); ?>
<?= \yii\helpers\Html::a(
        \kartik\icons\Icon::show('refresh') . Yii::t('app', 'Refresh'),
        ['refresh'],
        ['class' => 'btn btn-success']
    );?>
<?php $this->endBlock(); ?>

<div class="events-index">
    <?=
    DynaGrid::widget(
        [
            'options' => [
                'id' => 'events-grid',
            ],
            'theme' => 'panel-default',
            'gridOptions' => [
                'dataProvider' => $dataProvider,
                'hover' => true,
                'panel' => [
                    'heading' => Html::tag('h3', $this->title, ['class' => 'panel-title']),
                    'after' => $this->blocks['refresh-button'],
                ],
            ],
            'columns' => [
                'name',
                'class',
                'description',
                [
                    'class' => 'app\backend\components\ActionColumn',
                    'buttons' => function($model, $key, $index, $parent) {
                        /** @var \app\models\Plugins $model */
                        if (1 === $model->is_installed) {
                            /** @var \app\modules\plugin\components\AbstractPlugin $pluginClass */
                            $pluginClass = $model->class;
                            $buttons = [
                                [
                                    'url' => 'active',
                                    'icon' => 1 === $model->is_active ? 'close' : 'check',
                                    'class' => 1 === $model->is_active ? 'btn-warning' : 'btn-success',
                                    'label' => Yii::t('app', 1 === $model->is_active ? 'Deactivate' : 'Activate'),
                                ],
                            ];
                            if (!empty($pluginClass::getConfigureUrl())) {
                                $buttons[] = [
                                    'url' => $pluginClass::getConfigureUrl(),
                                    'icon' => 'cogs',
                                    'class' => 'btn-primary',
                                    'label' => Yii::t('app', 'Configure'),
                                ];
                            }
                            $buttons[] = [
                                'url' => 'install',
                                'icon' => 'power-off',
                                'class' => 'btn-danger',
                                'label' => Yii::t('app', 'Uninstall'),
                            ];

                            return $buttons;
                        } else {
                            return [
                                [
                                    'url' => 'install',
                                    'icon' => 'server',
                                    'class' => 'btn-success',
                                    'label' => Yii::t('app', 'Install'),
                                ],
                            ];
                        }
                    },
                ],
            ],
        ]
    );
    ?>
</div>