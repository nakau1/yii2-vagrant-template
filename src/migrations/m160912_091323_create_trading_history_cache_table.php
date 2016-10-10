<?php

use yii\db\Migration;

/**
 * Handles the creation for table `trading_history_cache`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m160912_091323_create_trading_history_cache_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('trading_history_cache', [
            'id'              => $this->primaryKey(),
            'pollet_id'       => $this->integer()->notNull(),
            'title'           => $this->string(60)->notNull(),
            'spent_value'     => $this->integer()->notNull(),
            'trading_date'    => $this->timestamp()->notNull(),
            'modified_date'   => $this->timestamp()->null(),
            'registered_date' => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-trading_history_cache-pollet_id',
            'trading_history_cache',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-trading_history_cache-pollet_id',
            'trading_history_cache',
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
            'fk-trading_history_cache-pollet_id',
            'trading_history_cache'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-trading_history_cache-pollet_id',
            'trading_history_cache'
        );

        $this->dropTable('trading_history_cache');
    }
}
