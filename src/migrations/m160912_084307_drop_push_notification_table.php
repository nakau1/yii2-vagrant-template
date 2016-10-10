<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `push_notification`.
 */
class m160912_084307_drop_push_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-push_notification-pollet_id',
            'push_notification'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-push_notification-pollet_id',
            'push_notification'
        );

        $this->dropTable('push_notification');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('push_notification', [
            'id'              => $this->primaryKey(),
            'pollet_id'       => $this->integer()->notNull(),
            'is_read'         => $this->smallInteger(1)->notNull()->defaultValue(false),
            'type'            => $this->string(35)->notNull(),
            'modified_date'   => $this->timestamp()->null(),
            'registered_date' => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-push_notification-pollet_id',
            'push_notification',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-push_notification-pollet_id',
            'push_notification',
            'pollet_id',
            'user',
            'pollet_id',
            'CASCADE',
            'CASCADE'
        );
    }
}
