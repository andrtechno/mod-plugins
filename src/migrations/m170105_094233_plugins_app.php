<?php

use panix\mod\plugins\BasePlugin;

class m170105_094233_plugins_app extends \panix\mod\plugins\migrations\Migration
{

    public function up()
    {
        $this->createTable($this->tn(self::TBL_APP), [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->insert($this->tn(self::TBL_APP), [
            'id' => BasePlugin::APP_WEB,
            'name' => 'web',
        ]);

        $this->insert($this->tn(self::TBL_APP), [
            'id' => BasePlugin::APP_COMMON,
            'name' => 'common',
        ]);

        $this->insert($this->tn(self::TBL_APP), [
            'id' => BasePlugin::APP_BACKEND,
            'name' => 'backend',
        ]);

        $this->insert($this->tn(self::TBL_APP), [
            'id' => BasePlugin::APP_API,
            'name' => 'api',
        ]);

        $this->insert($this->tn(self::TBL_APP), [
            'id' => BasePlugin::APP_CONSOLE,
            'name' => 'console',
        ]);
    }

    public function down()
    {
        $this->dropTable($this->tn(self::TBL_APP));
    }
}