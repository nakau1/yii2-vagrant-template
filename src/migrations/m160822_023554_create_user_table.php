<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user`.
 */
class m160822_023554_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'pollet_id'            => $this->primaryKey(),
            'cedyna_id'            => $this->bigInteger()->null()->unique(),
            'password'             => $this->string(256)->null(),
            'total_point'          => $this->decimal(9, 1)->notNull()->defaultValue(0),
            'mail_address'         => $this->string(256)->null(),
            'registration_status'  => $this->string(35)->notNull(),
            'unread_notifications' => $this->integer()->notNull()->defaultValue(0),
            'modified_date'        => $this->timestamp()->null(),
            'registered_date'      => $this->timestamp()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
