<?php

namespace panix\mod\plugins\models\query;

use panix\mod\plugins\models\Event;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Event]].
 *
 * @see Event
 */
class ShortcodeQuery extends ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Event[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Event|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}