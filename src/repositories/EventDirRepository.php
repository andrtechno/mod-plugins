<?php

namespace panix\mod\plugins\repositories;

use panix\mod\plugins\BasePlugin;
use panix\mod\plugins\models\Category;

class EventDirRepository extends EventRepository
{
    /**
     * @param BasePlugin $pluginClass
     */
    public function populate($pluginClass)
    {
        $pos = 0;
        foreach ($pluginClass::events() as $className => $events) {
            $pos++;
            foreach ($events as $eventName => $handler) {
                $handlerMethod = is_array($handler) ? $handler[0] : $handler;
                $this->_data[] = [
                    'app_id' => $this->checkApp($pluginClass),
                    'category_id' => Category::CAT_PLUGINS,
                    'trigger_class' => $className,
                    'trigger_event' => $eventName,
                    'handler_class' => $pluginClass,
                    'handler_method' => $handlerMethod,
                    'pos' => $pos,
                    'data' => isset($handler[1]) ? $handler[1] : []
                ];
            }
        };
    }

    /**
     * Convert string AppId to int app_id
     * @param $pluginClass
     * @return int $app_id
     */
    protected function checkApp($pluginClass)
    {
        if (!isset($pluginClass::$appId)) return BasePlugin::APP_WEB;
        return $pluginClass::$appId;
    }
}