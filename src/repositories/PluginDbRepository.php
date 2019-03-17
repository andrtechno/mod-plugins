<?php

namespace panix\mod\plugins\repositories;

use panix\mod\plugins\models\Event;
use panix\mod\plugins\models\Plugin;
use panix\mod\plugins\models\Shortcode;
use yii\helpers\Html;

class PluginDbRepository extends PluginRepository
{
    /**
     * @param $id
     * @return Plugin
     * @throws \InvalidArgumentException
     */
    public function find($id)
    {
        if (!$item = Plugin::findOne($id)) {
            throw new \InvalidArgumentException('Model not found');
        }
        return $item;
    }

    /**
     * @return array|\panix\mod\plugins\models\Plugin[]
     */
    public function findAll()
    {
        $items = Plugin::find()->where(['<>', 'id', Plugin::EVENTS_CORE])->all();
        return $items;
    }

    /**
     * @param $hash
     * @return Plugin|null
     */
    public function findByHash($hash)
    {
        if (!$item = Plugin::find()->where(['hash' => $hash])->limit(1)->one()) {
            throw new \InvalidArgumentException('Model not found');
        }
        return $item;
    }

    /**
     * populate data
     */
    protected function populate()
    {
        $this->_data = Plugin::find()->where(['<>', 'id', Plugin::EVENTS_CORE])->asArray()->all();
    }

    /**
     * @param Plugin $item
     * @throws \Exception
     */
    public function add(Plugin $item)
    {
        if (!$item->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model is exists');
        }
        if (!$item->insert()) {
            throw new \Exception(Html::errorSummary($item));
        }
    }

    /**
     * @param Plugin $item
     * @throws \Exception
     */
    public function save(Plugin $item)
    {
        if ($item->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        if (!$item->update()) {
            throw new \Exception(Html::errorSummary($item));
        }
    }

    /**
     * @param $hash
     * @param array $data
     * @return Plugin|null
     */
    public function savePlugin($hash, $data)
    {
        $data = (array) new PluginDbRepositoryMap($data);
        $model = $this->findByHash($hash);
        $model->setAttributes($data);
        $this->save($model);
        return $model;
    }

    /**
     * @param array $data
     * @return Plugin
     */
    public function addPlugin($data)
    {
        $data = (array) new PluginDbRepositoryMap($data);
        $model = new Plugin();
        $model->setAttributes($data);
        $this->add($model);
        return $model;
    }

    /**
     * @param Plugin $model
     * @param Event $event
     */
    public function linkEvent(Plugin $model, Event $event){
        $model->link('events', $event);
    }

    /**
     * @param Plugin $model
     * @param Shortcode $shortcode
     */
    public function linkShortcode(Plugin $model, Shortcode $shortcode){
        $model->link('shortcodes', $shortcode);
    }

}