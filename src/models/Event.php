<?php

namespace panix\mod\plugins\models;

use Yii;
use panix\mod\plugins\helpers\JsonHelper;
use panix\mod\plugins\models\query\EventQuery;
use panix\mod\plugins\validators\CallableValidator;
use panix\mod\plugins\validators\ClassNameValidator;
use panix\mod\plugins\validators\JsonValidator;
use panix\engine\db\ActiveRecord;

/**
 * This is the model class for table "{{%plugins__event}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $category_id
 * @property integer $plugin_id
 * @property string $trigger_class
 * @property string $trigger_event
 * @property string $handler_class
 * @property string $handler_method
 * @property string $text
 * @property string $data
 * @property integer $status
 * @property Plugin $plugin
 * @property Category $category
 */
class Event extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugins__event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'trigger_class', 'trigger_event', 'handler_class', 'handler_method'], 'required'],
            [['plugin_id', 'category_id', 'status', 'pos'], 'integer'],
            [['trigger_class', 'trigger_event', 'handler_class', 'handler_method'], 'string', 'max' => 255],
            [['pos'], 'default', 'value' => 1],
            [['data'], JsonValidator::class],
            [['handler_class', 'trigger_class'], ClassNameValidator::class],
            [['handler_method'], CallableValidator::class, 'callableAttribute' => 'handler_class']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('plugins/default', 'ID'),
            'app_id' => Yii::t('plugins/default', 'App ID'),
            'plugin_id' => Yii::t('plugins/default', 'Plugin ID'),
            'category_id' => Yii::t('plugins/default', 'Category'),
            'trigger_class' => Yii::t('plugins/default', 'Trigger Class'),
            'trigger_event' => Yii::t('plugins/default', 'Trigger Event'),
            'handler_class' => Yii::t('plugins/default', 'Handler Class'),
            'handler_method' => Yii::t('plugins/default', 'Handler Method'),
            'data' => Yii::t('plugins/default', 'Data'),
            'pos' => Yii::t('plugins/default', 'Position'),
            'status' => Yii::t('plugins/default', 'Status'),
            'text' => Yii::t('plugins/default', 'Text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlugin()
    {
        return $this->hasOne(Plugin::class, ['id' => 'plugin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @inheritdoc
     * @return EventQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EventQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getTriggerClass()
    {
        return $this->trigger_class;
    }

    /**
     * @return string
     */
    public function getTriggerEvent()
    {
        return $this->trigger_event;
    }

    /**
     * @return array
     */
    public function getHandler()
    {
        return [
            [$this->handler_class, $this->handler_method],
            JsonHelper::decode($this->data)
        ];
    }
}
