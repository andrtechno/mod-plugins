<?php

namespace panix\mod\plugins\dto;

use panix\mod\plugins\helpers\JsonHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class EventsDiffDto
 * @package panix\mod\plugins\dto
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class EventsDiffDto
{
    protected $_data = [];

    /**
     * PluginsDiffDto constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $item) {
            $diff = [];
            $diff['handler_class'] = ArrayHelper::getValue($item, 'handler_class');
            $diff['handler_method'] = ArrayHelper::getValue($item, 'handler_method');
            $config = ArrayHelper::getValue($item, 'data', null);
            if ($config) {
                $diff['data'] = $this->prepareConfig($config); // if added new config
            }
            $this->_data[$diff['handler_class']] = Json::encode($diff);
        }
    }

    /**
     * @return array
     */
    public function getDiff()
    {
        return $this->_data;
    }

    /**
     * @param $data
     * @return array
     */
    protected function prepareConfig($data)
    {
        return array_keys(JsonHelper::decode($data));
    }
}
