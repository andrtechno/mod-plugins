<?php

namespace panix\mod\plugins\models;

use panix\mod\plugins\models\query\PluginQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%plugins__plugin}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $version
 * @property string $text
 * @property string $author
 * @property string $author_url
 * @property string $hash
 * @property integer $status
 *
 * @property Event[] $pluginsEvents
 */
class Plugin extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const EVENTS_CORE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugins__plugin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['text'], 'string'],
            [['status'], 'integer'],
            [['name', 'url', 'version', 'author', 'author_url'], 'string', 'max' => 255],
            [['hash'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('plugins/default', 'ID'),
            'name' => Yii::t('plugins/default', 'Name'),
            'url' => Yii::t('plugins/default', 'Url'),
            'version' => Yii::t('plugins/default', 'Version'),
            'text' => Yii::t('plugins/default', 'Text'),
            'author' => Yii::t('plugins/default', 'Author'),
            'author_url' => Yii::t('plugins/default', 'Author Url'),
            'status' => Yii::t('plugins/default', 'Status'),
            'hash' => Yii::t('plugins/default', 'Hash'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::class, ['plugin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShortcodes()
    {
        return $this->hasMany(Shortcode::class, ['plugin_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return PluginQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PluginQuery(get_called_class());
    }
}
