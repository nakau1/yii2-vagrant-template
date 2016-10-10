<?php

use yii\db\Migration;

/**
 * Handles the creation for table `push_information_opening`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `information`
 */
class m160912_084922_create_push_information_opening_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('push_information_opening', [
            'id'              => $this->primaryKey(),
            'pollet_id'       => $this->integer()->notNull(),
            'information_id'  => $this->integer()->notNull(),
            'registered_date' => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-push_information_opening-pollet_id',
            'push_information_opening',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-push_information_opening-pollet_id',
            'push_information_opening',
            'pollet_id',
            'user',
            'pollet_id',
            'CASCADE'
        );

        // creates index for column `information_id`
        $this->createIndex(
            'idx-push_information_opening-information_id',
            'push_information_opening',
            'information_id'
        );

        // add foreign key for table `information`
        $this->addForeignKey(
            'fk-push_information_opening-information_id',
            'push_information_opening',
            'information_id',
            'information',
            'id',
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
            'fk-push_information_opening-pollet_id',
            'push_information_opening'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-push_information_opening-pollet_id',
            'push_information_opening'
        );

        // drops foreign key for table `information`
        $this->dropForeignKey(
            'fk-push_information_opening-information_id',
            'push_information_opening'
        );

        // drops index for column `information_id`
        $this->dropIndex(
            'idx-push_information_opening-information_id',
            'push_information_opening'
        );

        $this->dropTable('push_information_opening');
    }
}
