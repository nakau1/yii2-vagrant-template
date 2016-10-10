<?php

use yii\db\Migration;

/**
 * Handles the creation for table `inquiry`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m160912_090718_create_inquiry_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('inquiry', [
            'id'              => $this->primaryKey(),
            'pollet_id'       => $this->integer()->notNull(),
            'mail_address'    => $this->string(256)->notNull(),
            'content'         => $this->text()->notNull(),
            'modified_date'   => $this->timestamp()->null(),
            'registered_date' => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-inquiry-pollet_id',
            'inquiry',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-inquiry-pollet_id',
            'inquiry',
            'pollet_id',
            'user',
            'pollet_id',
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
            'fk-inquiry-pollet_id',
            'inquiry'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-inquiry-pollet_id',
            'inquiry'
        );

        $this->dropTable('inquiry');
    }
}
