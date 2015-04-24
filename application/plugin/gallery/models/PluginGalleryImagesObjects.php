<?php

namespace app\plugin\gallery\models;

use Yii;

/**
 * This is the model class for table "{{%plugin_gallery_images_objects}}".
 *
 * @property integer $id
 * @property integer $object_id
 * @property integer $object_model_id
 * @property integer $image_id
 * @property integer $sort_order
 */
class PluginGalleryImagesObjects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin_gallery_images_objects}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_id', 'object_model_id', 'image_id'], 'required'],
            [['object_id', 'object_model_id', 'image_id', 'sort_order'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'object_id' => Yii::t('app', 'Object ID'),
            'object_model_id' => Yii::t('app', 'Object Model ID'),
            'image_id' => Yii::t('app', 'Image ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
        ];
    }
}
