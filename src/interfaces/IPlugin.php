<?php

namespace panix\mod\plugins\interfaces;

/**
 * Interface IPlugin
 * @package panix\mod\plugins\components
 */
interface IPlugin
{
    /**
     *  [
     *      'yii\base\View' => [
     *          'afterRender' => ['hello', self::$config]
     *      ]
     *  ];
     * @return array
     */
    public static function events();
}