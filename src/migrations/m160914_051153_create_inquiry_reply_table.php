<?php

use yii\db\Migration;

/**
 * Handles the creation for table `inquiry_reply`.
 * Has foreign keys to the tables:
 *
 * - `admin_user`
 */
class m160914_051153_create_inquiry_reply_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('inquiry_reply', [
            'id'            => $this->primaryKey(),
            'inquiry_id'    => $this->integer()->notNull(),
            'admin_user_id' => $this->integer()->notNull(),
            'content'       => $this->text()->notNull(),
            'updated_at'    => $this->integer()->null(),
            'created_at'    => $this->integer()->null(),
        ]);

        // creates index for column `admin_user_id`
        $this->createIndex(
            'idx-inquiry_reply-admin_user_id',
            'inquiry_reply',
            'admin_user_id'
        );

        // add foreign key for table `admin_user`
        $this->addForeignKey(
            'fk-inquiry_reply-admin_user_id',
            'inquiry_reply',
            'admin_user_id',
            'admin_user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `admin_user`
        $this->dropForeignKey(
            'fk-inquiry_reply-admin_user_id',
            'inquiry_reply'
        );

        // drops index for column `admin_user_id`
        $this->dropIndex(
            'idx-inquiry_reply-admin_user_id',
            'inquiry_reply'
        );

        $this->dropTable('inquiry_reply');
    }
}
