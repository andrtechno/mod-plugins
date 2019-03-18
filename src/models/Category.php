<?php

namespace panix\mod\plugins\models;

use panix\mod\plugins\models\query\CategoryQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%plugins__category}}".
 *
 * @property integer $id
 * @property string $name
 */
class Category extends ActiveRecord
{
    const CAT_PLUGINS = 1;
    const CAT_SHORTCODES = 2;
    const CAT_SEO = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugins__category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * @inheritdoc
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::class, ['category_id' => 'id']);
    }

}
