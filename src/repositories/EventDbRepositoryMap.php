<?php

namespace panix\mod\plugins\repositories;

use panix\mod\plugins\helpers\JsonHelper;
use panix\mod\plugins\models\Event;

/**
 * Class EventDbRepositoryMap
 * @package panix\mod\plugins\repositories
 */
class EventDbRepositoryMap
{
    public $id;
    public $app_id;
    public $category_id;
    public $trigger_class;
    public $trigger_event;
    public $handler_class;
    public $handler_method;
    public $data;
    public $text;
    public $pos;
    public $status = Event::STATUS_ACTIVE;

    /**
     * PluginDataDto constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        if ($this->data) {
            $this->data = JsonHelper::encode($this->data);
        }
    }
}
