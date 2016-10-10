<?php

use yii\db\Migration;

/**
 * Handles the creation for table `push_notification_token`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m160822_054148_create_push_notification_token_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('push_notification_token', [
            'id' => $this->primaryKey(),
            'pollet_id' => $this->integer()->notNull(),
            'device_id' => $this->string(256)->notNull(),
            'token' => $this->string(256)->notNull(),
            'platform' => $this->string(20)->notNull(),
            'modified_date'        => $this->timestamp()->null(),
            'registered_date'      => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-push_notification_token-pollet_id',
            'push_notification_token',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-push_notification_token-pollet_id',
            'push_notification_token',
            'pollet_id',
            'user',
            'pollet_id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-push_notification_token-pollet_id',
            'push_notification_token'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-push_notification_token-pollet_id',
            'push_notification_token'
        );

        $this->dropTable('push_notification_token');
    }
}
