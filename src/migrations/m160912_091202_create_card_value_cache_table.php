<?php

use yii\db\Migration;

/**
 * Handles the creation for table `card_value_cache`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m160912_091202_create_card_value_cache_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('card_value_cache', [
            'id'              => $this->primaryKey(),
            'pollet_id'       => $this->integer()->notNull(),
            'value'           => $this->integer()->notNull(),
            'modified_date'   => $this->timestamp()->null(),
            'registered_date' => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-card_value_cache-pollet_id',
            'card_value_cache',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-card_value_cache-pollet_id',
            'card_value_cache',
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
            'fk-card_value_cache-pollet_id',
            'card_value_cache'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-card_value_cache-pollet_id',
            'card_value_cache'
        );

        $this->dropTable('card_value_cache');
    }
}
