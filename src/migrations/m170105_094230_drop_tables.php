<?php
//namespace panix\mod\plugins\migrations;

class m170105_094230_drop_tables extends \panix\mod\plugins\migrations\Migration
{
    public function up()
    {
        if (in_array($this->tn('event'), $this->getDb()->schema->tableNames)) {
            $this->dropTable($this->tn('event'));
        }

        if (in_array($this->tn('item'), $this->getDb()->schema->tableNames)) {
            $this->dropTable($this->tn('item'));
        }

        if (in_array($this->tn('app'), $this->getDb()->schema->tableNames)) {
            $this->dropTable($this->tn('app'));
        }
    }

    public function down()
    {
        //echo "m170105_094230_drop_tables cannot be reverted.\n";
        //return false;
    }
}