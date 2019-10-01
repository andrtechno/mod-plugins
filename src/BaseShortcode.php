<?php
namespace panix\mod\plugins;

use panix\mod\plugins\interfaces\IShortcode;

/**
 * Class BaseShorcode
 * @package panix\mod\plugins
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
abstract class BaseShortcode implements IShortcode
{
    const APP_WEB = 1;
    const APP_BACKEND = 2;
    const APP_COMMON = 3;

    const SHORTCODES_METHOD = 'shortcodes';

    /**
     * Application id, where plugin will be worked.
     * Support values: web, backend, common, api
     * Default: web
     * @var string $appId
     */
    public static $appId = self::APP_WEB;

}