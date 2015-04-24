<?php

namespace app\plugin\gallery\models;

use Yii;

/**
 * This is the model class for table "{{%plugin_gallery_galleries}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $description
 * @property string $options
 * @property integer $sort_order
 */
class PluginGalleryGalleries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin_gallery_galleries}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'path'], 'required'],
            [['sort_order'], 'integer'],
            [['name', 'path', 'description'], 'string', 'max' => 255],
            [['options'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'path' => Yii::t('app', 'Path'),
            'description' => Yii::t('app', 'Description'),
            'options' => Yii::t('app', 'Options'),
            'sort_order' => Yii::t('app', 'Sort Order'),
        ];
    }
}
?>