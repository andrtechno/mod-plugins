<?php

namespace panix\mod\plugins;

use Yii;
use yii\base\InvalidConfigException;
use panix\engine\WebModule;

class Module extends WebModule
{
   // public $controllerNamespace = 'panix\mod\plugins\controllers';
   // public $defaultRoute = 'plugin';

    // Directory
    public $pluginsDir;

    public function init2()
    {
        parent::init();

        /*if (!isset(Yii::$app->i18n->translations['plugin'])) {
            Yii::$app->i18n->translations['plugin'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@lo/plugins/messages'
            ];
        }*/

        //user did not define the Navbar?
        if (!$this->pluginsDir) {
          throw new InvalidConfigException('"pluginsDir" must be set');
        }
    }
}
