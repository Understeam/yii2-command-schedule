<?php

use yii\db\Migration;

class m160513_000000_scheduler_task extends Migration
{

    public function up()
    {
        $this->createTable('{{%scheduler_task}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(),
            'status' => $this->smallInteger(),
            'command' => $this->text(),
            'expression' => $this->string(),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
        ]);
        $this->createIndex('idx-scheduler_task-key', '{{%scheduler_task}}', 'key');
        $this->createIndex('idx-scheduler_task-status', '{{%scheduler_task}}', 'status');
        $this->createIndex('idx-scheduler_task-key_status', '{{%scheduler_task}}', ['key', 'status']);
    }

    public function down()
    {
        $this->dropTable('{{%scheduler_task}}');
    }

}