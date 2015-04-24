<?php

namespace app\plugin\gallery\models;

use Yii;

/**
 * This is the model class for table "{{%plugin_gallery_thumbs}}".
 *
 * @property integer $id
 * @property integer $image_id
 * @property string $thumb_type
 * @property string $file_name
 * @property string $file_path
 */
class PluginGalleryThumbs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin_gallery_thumbs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image_id', 'thumb_type', 'file_name', 'file_path'], 'required'],
            [['image_id'], 'integer'],
            [['thumb_type', 'file_name', 'file_path'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'image_id' => Yii::t('app', 'Image ID'),
            'thumb_type' => Yii::t('app', 'Thumb Type'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_path' => Yii::t('app', 'File Path'),
        ];
    }
}
