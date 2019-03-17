<?php
namespace panix\mod\plugins\interfaces;

/**
 * Interface IPlugin
 * @package panix\mod\plugins\components
 */
interface IShortcode
{
    /**
     *  [
     *      'code' => ['panix\mod\plugins\plugins\code\Code', 'widget'],
     *      'anothershortcode'=>function($attrs, $content, $tag){
     *          .....
     *      },
     *  ];
     * @return array
     */
    public static function shortcodes();
}