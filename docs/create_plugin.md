# Create your plugin

To create your plugin you need to run the following required steps

### 1. Create in dir with our plugins `@common\plugins` new folder:
* For example: `test`

### 2. In this folder create:
* `README.md` with usage instruction for this plugin
* New class `TestPlugin`, with information about plugin

```php

use lo\plugins\BasePlugin;

namespace common\plugins\test;

/**
 * Plugin Name: Test plugin
 * Plugin URI:
 * Version: 1.0
 * Description: Small test plugin
 * Author: Andrey Lukyanov
 * Author URI: https://github.com/loveorigami
 */
 
class TestPlugin extends BasePlugin
{
...
}

```

* Add static property `$appId`

```php

    /**
     * Application id, where plugin will be worked.
     * @var appId integer
     */
    public static $appId = self::APP_FRONTEND;

```

* And default configuration

```php
    /**
     * Default configuration for plugin.
     * @var config array()
     */
    public static $config = [
        'term' => 'Hello, world!',
    ];
```

* Then, assign a template events

```php
    public static function events()
    {
        return [
            $eventSenderClassName => [
                $eventName => [$handlerMethodName, self::$config]
            ],
        ];
    }
```

for example:

```php
    public static function events()
    {
        return [
            yii\web\Response::class => [
                yii\web\Response::EVENT_AFTER_PREPARE => ['foo', self::$config]
            ],
        ];
    }
```
more about `$eventSenderClassName` and `$eventName` you can be found on the info tab of this module

!["Info tab"](img/tab_info.jpg)

* Create a handler method `foo` with the necessary logic

```php
    /**
     * handler method foo
     */
    public static function foo($event)
    {
        $term = ($event->data['term']) ? $event->data['term'] : self::$config['term'];
        $event->sender->content =  str_replace($term,"<h1>$term</h1>", $event->sender->content);
        return true;
    }
```

* That's all. Then you have to [install](install_plugin.md) this plugin