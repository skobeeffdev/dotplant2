<?php

use yii\db\Schema;
use yii\db\Migration;

class m150410_115349_events extends Migration
{
    public function up()
    {
        $this->createTable('{{%events}}',
            [
                'id' => Schema::TYPE_PK,
                'class' => Schema::TYPE_STRING . ' NOT NULL',
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'description' => Schema::TYPE_STRING,
                'options' => Schema::TYPE_TEXT,
                'is_active' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
                'is_deleted' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
            ]
        );

        $this->createIndex('idx_events_class', '{{%events}}', 'class', true);
    }

    public function down()
    {
        $this->dropIndex('idx_events_class', '{{%events}}');
        $this->dropTable('{{%events}}');

        return true;
    }
}
?>