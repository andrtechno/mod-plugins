<?php

namespace panix\mod\plugins\models;

use Yii;
use panix\mod\plugins\BasePlugin;
use panix\mod\plugins\models\query\ShortcodeQuery;
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
 * @property string $handler_class
 * @property string $tag
 * @property string $tooltip
 * @property string $data
 * @property string $text
 * @property integer $status
 *
 * @property Plugin $plugin
 * @property Category $category
 */
class Shortcode extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugins__shortcode}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'handler_class'], 'required'],
            [['plugin_id', 'app_id', 'category_id', 'status'], 'integer'],
            [['text', 'tag', 'tooltip'], 'string'],
            [['data'], JsonValidator::class],
            [['handler_class'], ClassNameValidator::class],
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
            'handler_class' => Yii::t('plugins/default', 'Handler Class'),
            'tag' => Yii::t('plugins/default', 'Tag'),
            'tooltip' => Yii::t('plugins/default', 'Tooltip'),
            'data' => Yii::t('plugins/default', 'Data'),
            'text' => Yii::t('plugins/default', 'Text'),
            'status' => Yii::t('plugins/default', 'Status')
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
     * @return ShortcodeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShortcodeQuery(get_called_class());
    }

    /**
     * @param null $appId
     * @return array
     */
    public static function shortcodeList($appId = null)
    {
        if (!$appId) return [];

        return self::find()
            ->alias('s')
            ->innerJoinWith(['plugin p'])
            ->where(['AND',
                ['s.status' => self::STATUS_ACTIVE],
                ['p.status' => Plugin::STATUS_ACTIVE],
                ['s.app_id' => [$appId, BasePlugin::APP_COMMON]]
            ])
            ->all();
    }
}
