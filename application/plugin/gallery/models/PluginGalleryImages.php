<?php

namespace app\plugin\gallery\models;

use Yii;

/**
 * This is the model class for table "{{%plugin_gallery_images}}".
 *
 * @property integer $id
 * @property integer $gallery_id
 * @property string $file_name
 * @property string $file_path
 * @property string $description
 * @property integer $sort_order
 */
class PluginGalleryImages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin_gallery_images}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gallery_id', 'sort_order'], 'integer'],
            [['file_name', 'file_path'], 'required'],
            [['file_name', 'file_path', 'description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'gallery_id' => Yii::t('app', 'Gallery ID'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_path' => Yii::t('app', 'File Path'),
            'description' => Yii::t('app', 'Description'),
            'sort_order' => Yii::t('app', 'Sort Order'),
        ];
    }
}
