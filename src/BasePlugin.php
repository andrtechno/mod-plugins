<?php
namespace panix\mod\plugins;

use panix\mod\plugins\interfaces\IPlugin;

/**
 * Class BasePlugin
 * @package panix\mod\plugins
 */
abstract class BasePlugin implements IPlugin
{
    const APP_WEB = 1;
    const APP_BACKEND = 2;
    const APP_COMMON = 3;
    const APP_API = 4;
    const APP_CONSOLE = 5;


    /**
     * Application id, where plugin will be worked.
     * Support values: web, backend, common, api
     * Default: web
     * @var string $appId
     */
    public static $appId = self::APP_WEB;

    /**
     * Default configuration for plugin.
     * @var array $config
     */
    public static $config = [];

}