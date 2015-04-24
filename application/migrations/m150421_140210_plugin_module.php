<?php

use yii\db\Schema;
use yii\db\Migration;

class m150421_140210_plugin_module extends Migration
{
    public function up()
    {
        $this->createTable('{{%plugins}}',
            [
                'id' => Schema::TYPE_PK,
                'class' => Schema::TYPE_STRING . ' NOT NULL',
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'description' => Schema::TYPE_STRING,
                'options' => Schema::TYPE_TEXT,
                'is_active' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
                'is_installed' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
                'is_deleted' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
            ]
        );
        $this->createIndex('idx_plugins_class', '{{%plugins}}', 'class', true);
    }

    public function down()
    {
        $this->dropTable('{{%plugins}}');

        return true;
    }
}
?>