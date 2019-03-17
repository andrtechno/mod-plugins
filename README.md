# Getting started with mod-plugins


mod-plugins is designed to work out of the box. It means that installation requires
minimal steps. Only one configuration step should be taken and you are ready to
have plugin system on your Yii2 website.

### 1. Download

mod-plugins can be installed using composer. Run following command to download and
install mod-plugins:

```bash
composer require "panix/mod-plugins": "*"
```

### 2. Update database schema

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component,
add in our console config namespace migration - [more here](http://www.yiiframework.com/doc-2.0/guide-db-migrations.html#namespaced-migrations)

```php
return [
    'controllerMap' => [
        'migrate' => [
            'class' => 'panix\engine\console\controllers\MigrateController',
            'migrationNamespaces' => [
                 ...
                'panix\mod\plugins\migrations'
            ],
        ],
    ],
];
```

and run the following command:

```php
$ php yii migrate
```

### 3. Configure application

Let's start with defining module in `@backend/config/main.php`:

```php
'modules' => [
    'plugins' => [
        'class' => 'panix\mod\plugins\Module',
        'pluginsDir'=>[
            '@panix/mod/plugins/core', // default dir with core plugins
            // '@common/plugins', // dir with our plugins
        ]
    ],
],
```
That's all, now you have module installed and configured in advanced template.

Next, open `@frontend/config/main.php` and add following:

```php
...
'bootstrap' => ['log', 'plugins'],
...
'components' => [
    'plugins' => [
        'class' => panix\mod\plugins\components\PluginsManager::class,
        'appId' => 1 // panix\mod\plugins\BasePlugin::APP_FRONTEND,
        // by default
        'enablePlugins' => true,
        'shortcodesParse' => true,
        'shortcodesIgnoreBlocks' => [
            '<pre[^>]*>' => '<\/pre>',
            //'<div class="content[^>]*>' => '<\/div>',
        ]
    ],
    'view' => [
        'class' => panix\mod\plugins\components\View::class,
    ]
    ...
]
```

Also do the same thing with 
* `@backend/config/main.php`
* `@console/config/main.php`
* `@api/config/main.php`
* our modules 
* etc...

```php
...
'bootstrap' => ['log', 'plugins'],
...
'components' => [
    'plugins' => [
        'class' => panix\mod\plugins\components\PluginsManager::class,
        'appId' => panix\mod\plugins\BasePlugin::APP_BACKEND
    ],
    'view' => [
        'class' => panix\mod\plugins\components\View::class,
    ]
    ...
]
```

#### Base AppId ```panix\mod\plugins\BasePlugin::```
* const APP_FRONTEND = 1;
* const APP_BACKEND = 2;
* const APP_COMMON = 3;
* const APP_API = 4;
* const APP_CONSOLE = 5;


#### Core plugins (examples)

* [External links](src/core/extralinks)
* [Http Authentication](src/core/httpauth)
* [Hello world!](src/core/helloworld)

#### Your plugins

* [Create](docs/create_plugin.md)
* [Install](docs/install_plugin.md)

#### Автор & License
* Автор [LoveOrigami](https://github.com/loveorigami)
* [License](LICENSE.md)

> Модуль был взят у автор и под корректирован под Pixelion CMS
