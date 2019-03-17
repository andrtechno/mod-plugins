<?php

namespace panix\mod\plugins\repositories;

use panix\mod\plugins\BasePlugin;
use panix\mod\plugins\BaseShortcode;
use panix\mod\plugins\helpers\ClassHelper;
use yii\helpers\ArrayHelper;

class PluginDirRepository extends PluginRepository
{
    /**
     * @var array
     */
    protected $_dirs;

    /**
     * @param array $dirs
     */
    public function setDirs($dirs)
    {
        $this->_dirs = $dirs;
    }

    protected $_methods = ['events', 'shortcodes'];

    /**
     * populate pool storage
     */
    protected function populate()
    {
        ClassHelper::getAllClasses($this->_dirs, function ($class) {
            foreach ($this->_methods as $type) {
                /** @var BasePlugin|BaseShortcode $class */
                if (is_callable([$class, $type])) {
                    $this->_data[] = $this->getInfo($class, $type);
                    return $class;
                }
            }
            return null;
        });
    }

    /**
     * @param $pluginClass
     * @param $type
     * @return array
     */
    protected function getInfo($pluginClass, $type)
    {
        $plugin_info = ClassHelper::getPluginInfo($pluginClass);

        preg_match('|Plugin Name:(.*)$|mi', $plugin_info, $name);
        preg_match('|Plugin URI:(.*)$|mi', $plugin_info, $url);
        preg_match('|Version:(.*)$|mi', $plugin_info, $version);
        preg_match('|Description:(.*)$|mi', $plugin_info, $text);
        preg_match('|Author:(.*)$|mi', $plugin_info, $author);
        preg_match('|Author URI:(.*)$|mi', $plugin_info, $author_url);

        return [
            'handler_class' => $pluginClass,
            'type' => $type,
            'name' => trim(ArrayHelper::getValue($name, 1, 'plugin - ' . $pluginClass)),
            'url' => trim(ArrayHelper::getValue($url, 1)),
            'text' => trim(ArrayHelper::getValue($text, 1)),
            'author' => trim(ArrayHelper::getValue($author, 1)),
            'author_url' => trim(ArrayHelper::getValue($author_url, 1)),
            'new_version' => trim(ArrayHelper::getValue($version, 1, '1.0')),
            'new_hash' => md5($pluginClass)
        ];
    }
} 