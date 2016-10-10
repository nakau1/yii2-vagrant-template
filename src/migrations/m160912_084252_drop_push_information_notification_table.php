<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `push_information_notification`.
 */
class m160912_084252_drop_push_information_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        // drops foreign key for table `push_notification`
        $this->dropForeignKey(
            'fk-push_information_notification-push_notification_id',
            'push_information_notification'
        );

        // drops index for column `push_notification_id`
        $this->dropIndex(
            'idx-push_information_notification-push_notification_id',
            'push_information_notification'
        );

        // drops foreign key for table `information`
        $this->dropForeignKey(
            'fk-push_information_notification-information_id',
            'push_information_notification'
        );

        // drops index for column `information_id`
        $this->dropIndex(
            'idx-push_information_notification-information_id',
            'push_information_notification'
        );

        $this->dropTable('push_information_notification');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('push_information_notification', [
            'id'                   => $this->primaryKey(),
            'push_notification_id' => $this->integer()->notNull(),
            'information_id'       => $this->integer()->notNull(),
            'modified_date'        => $this->timestamp()->null(),
            'registered_date'      => $this->timestamp()->null(),
        ]);

        // creates index for column `push_notification_id`
        $this->createIndex(
            'idx-push_information_notification-push_notification_id',
            'push_information_notification',
            'push_notification_id'
        );

        // add foreign key for table `push_notification`
        $this->addForeignKey(
            'fk-push_information_notification-push_notification_id',
            'push_information_notification',
            'push_notification_id',
            'push_notification',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `information_id`
        $this->createIndex(
            'idx-push_information_notification-information_id',
            'push_information_notification',
            'information_id'
        );

        // add foreign key for table `information`
        $this->addForeignKey(
            'fk-push_information_notification-information_id',
            'push_information_notification',
            'information_id',
            'information',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
