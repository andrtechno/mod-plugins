<?php

namespace panix\mod\plugins;

use Yii;
use yii\base\InvalidConfigException;
use panix\engine\WebModule;

class Module extends WebModule
{
    // public $controllerNamespace = 'panix\mod\plugins\controllers';
    public $defaultRoute = 'plugin';

    // Directory
    public $pluginsDir;
    public $icon = 'chip';

    public function init()
    {
        parent::init();


        //user did not define the Navbar?
        if (!$this->pluginsDir) {
            throw new InvalidConfigException('"pluginsDir" must be set');
        }
    }


    public function getAdminMenu()
    {
        return [
            'system' => [
                'items' => [
                    [
                        'label' => Yii::t('plugins/default', 'MODULE_NAME'),
                        'url' => ['/admin/plugins/plugin/index'],
                        'icon' => $this->icon,
                        'visible' => Yii::$app->user->can('/plugins/admin/plugin/index') || Yii::$app->user->can('/plugins/admin/plugin/*'),
                    ],
                ],
            ]
        ];
    }
}
