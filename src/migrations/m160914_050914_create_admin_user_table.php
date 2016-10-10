<?php

use yii\db\Migration;

/**
 * Handles the creation for table `admin_user`.
 */
class m160914_050914_create_admin_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin_user', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string(32)->notNull(),
            'updated_at' => $this->integer()->null(),
            'created_at' => $this->integer()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin_user');
    }
}
