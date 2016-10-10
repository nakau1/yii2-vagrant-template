<?php

use yii\db\Migration;

class m160906_024508_create_batch_management extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('batch_management', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'status' => $this->string(15)->notNull(),
        ]);
        $this->createIndex(
            'idx-name',
            'batch_management',
            ['name'],
            true
        );
    }

    public function down()
    {
        $this->dropTable('batch_management');
    }
}
