<?php

namespace panix\mod\plugins\migrations;

use panix\mod\plugins\models\Category;
use panix\mod\plugins\models\Event;
use panix\mod\plugins\models\Plugin;

/**
 * Custom migration which makes sure InnoDB with UTF-8 is preferred when using MySQL.
 */
class Migration extends \yii\db\Migration
{
    const TBL_APP = 'app';
    const TBL_CATEGORY = 'category';
    const TBL_PLUGIN = 'plugin';
    const TBL_EVENT = 'event';
    const TBL_SHORTCODE = 'shortcode';

    const CAT_PLUGINS = Category::CAT_PLUGINS;
    const CAT_SHORTCODES = Category::CAT_SHORTCODES;
    const CAT_SEO = Category::CAT_SEO;

    const EVENTS_CORE = Plugin::EVENTS_CORE;
    const PLUGIN_ACTIVE = Plugin::STATUS_ACTIVE;

    const EVENTS_ACTIVE = Event::STATUS_ACTIVE;

    /**
     * @inheritdoc
     */
    public $tableGroup = 'plugins';

    public function createTable($table, $columns, $options = null)
    {
        if ($options === null && $this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        parent::createTable($table, $columns, $options);
    }

    /**
     * Real table name builder
     * @param string $name table name
     * @return string
     */
    protected function tn($name)
    {
        return '{{%' . $this->tableGroup . '__' . $name . '}}';
    }

    /**
     * Foreign key relation names generator
     * @param string $table1 first table in relation
     * @param string $table2 second table in relation
     * @return string
     */
    protected function fk($table1, $table2)
    {
        return 'fk_' . $this->tableGroup . '__' . $table1 . '_' . $table2;
    }

}