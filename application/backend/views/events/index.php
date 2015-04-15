<?php

/**
 * @var yii\web\View $this
 */

use kartik\dynagrid\DynaGrid;
use kartik\helpers\Html;

$this->title = Yii::t('app', 'Events');
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
                    'class' => \kartik\grid\EditableColumn::className(),
                    'attribute' => 'is_active',
                    'editableOptions' => [
                        'data' => [
                            0 =>  Yii::t('app', 'Inactive'),
                            1 =>  Yii::t('app', 'Active'),
                        ],
                        'inputType' => 'dropDownList',
                        'placement' => 'left',
                        'formOptions' => [
                            'action' => 'update-editable',
                        ],
                    ],
                    'format' => 'raw',
                    'value' => function (\app\models\Events $model) {
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
                    },
                ],
                'eventlist:raw',
                [
                    'class' => 'app\backend\components\ActionColumn',
                    'buttons' => function($model, $key, $index, $parent) {
                        return [
                            [
                                'url' => 'edit',
                                'icon' => 'pencil',
                                'class' => 'btn-primary',
                                'label' => Yii::t('app', 'Edit'),
                            ],
                        ];
                    },
                ],
            ],
        ]
    );
    ?>
</div>