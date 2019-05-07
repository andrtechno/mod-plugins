<?php

namespace panix\mod\plugins\helpers;

use panix\mod\plugins\BasePlugin;
use yii\bootstrap4\Html;

/**
 * Class Bs Bootstrap Html helper
 * @package panix\mod\plugins\helpers
 */
class BS extends Html
{
    /**
     * Bootstrap color modifier classes
     */
    const TYPE_SECONDARY = 'secondary';
    const TYPE_PRIMARY = 'primary';
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    public static function appLabel($app_id)
    {
        switch ($app_id) {
            case BasePlugin::APP_FRONTEND:
                return self::badge('F', self::TYPE_PRIMARY);
                break;
            case BasePlugin::APP_COMMON:
                return self::badge('C', self::TYPE_SUCCESS);
                break;
            case BasePlugin::APP_BACKEND:
                return self::badge('B', self::TYPE_DANGER);
                break;
            case BasePlugin::APP_CONSOLE:
                return self::badge('S', self::TYPE_WARNING);
                break;
            case BasePlugin::APP_API:
                return self::badge('A', self::TYPE_INFO);
                break;
            case BasePlugin::APP_WEB:
                return self::badge('W', self::TYPE_INFO);
                break;
            default:
                return self::badge('D', self::TYPE_SECONDARY);
        }
    }

    /**
     * Generates a label.
     *
     * @param string $content the label content
     * @param string $type the bootstrap label type - defaults to 'default'
     *                        - is one of 'default, 'primary', 'success', 'info', 'danger', 'warning'
     * @param array $options html options for the label container
     * @param string $prefix the css class prefix - defaults to 'label label-'
     * @param string $tag the label container tag - defaults to 'span'
     *
     * Example(s):
     * ~~~
     * echo BS::badge('Default');
     * echo BS::badge('Primary', BS::TYPE_PRIMARY);
     * echo BS::badge('Success', BS::TYPE_SUCCESS);
     * ~~~
     *
     * @see http://getbootstrap.com/components/#labels
     *
     * @return string
     */
    public static function badge($content, $type = '', $options = [], $prefix = 'badge badge-', $tag = 'span')
    {
        if (!$type) {
            $type = self::TYPE_SECONDARY;
        }
        $class = isset($options['class']) ? ' ' . $options['class'] : '';
        $options['class'] = $prefix . $type . $class;
        return static::tag($tag, $content, $options);
    }
}