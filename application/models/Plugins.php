<?php

namespace app\models;

use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%plugins}}".
 *
 * @property integer $id
 * @property string $class
 * @property string $name
 * @property string $description
 * @property string $options
 * @property integer $is_active
 * @property integer $is_deleted
 * @property integer $is_installed
 */
class Plugins extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugins}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class', 'name'], 'required'],
            [['options'], 'string'],
            [['is_active', 'is_deleted', 'is_installed'], 'integer'],
            [['class', 'name', 'description'], 'string', 'max' => 255],
            [['class'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'class' => Yii::t('app', 'Class'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'options' => Yii::t('app', 'Options'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_installed' => Yii::t('app', 'Is Installed'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \devgroup\TagDependencyHelper\ActiveRecordHelper::className(),
            ],
        ];
    }

    static public function getPluginsAll($isFetched = true)
    {
        $query = static::find()->where(['is_deleted' => 0]);
        if ($isFetched) {
            return $query->all();
        }

        return $query;
    }
}
?>