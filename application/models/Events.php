<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%events}}".
 *
 * @property integer $id
 * @property string $class
 * @property string $name
 * @property string $description
 * @property string $options
 * @property integer $is_active
 * @property integer $is_deleted
 */
class Events extends \yii\db\ActiveRecord
{
    protected $optionsData = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%events}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class', 'name'], 'required'],
            [['options'], 'string'],
            [['is_active', 'is_deleted'], 'integer'],
            [['is_active', 'is_deleted'], 'default', 'value' => 0],
            [['class', 'name', 'description'], 'string', 'max' => 255],
            [['class'], 'unique']
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
            'is_active' => Yii::t('app', 'Active'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    public function getAllEvents()
    {
        return $this->optionsData['events'];
    }

    public function getCallback($callback = 'handle')
    {
        return [$this->class, $callback];
    }

    static public function createHandler($config)
    {
        $exists = static::findOne(['class' => $config['class']]);
        if (empty($exists)) {
            $new = new static;
                $new->class = $config['class'];
                $new->name = $config['name'];
                $new->description = $config['description'];
                $new->options = Json::encode($config['options']);
            $new->save();
        } else {
            $exists->is_deleted = 0;
            $exists->save();
        }
    }

    static public function getHandlersAll($isFetched = true)
    {
        $query = static::find()->where(['is_deleted' => 0]);
        if ($isFetched) {
            return $query->all();
        }

        return $query;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->optionsData = Json::decode($this->options);
    }

    public function beforeSave($insert)
    {
        $result = parent::beforeSave($insert);

        if (!$insert) {
            $this->options = Json::encode($this->optionsData);
        }

        return $result;
    }

    public function getEventList()
    {
        $result = [];
        foreach ($this->optionsData['events'] as $event) {
            if (is_array($event)) {
                $result[] = key($event);
            } else {
                $result[] = $event;
            }
        }

        return implode('<br />', $result);
    }
}
?>