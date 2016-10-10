<?php

use yii\db\Migration;

/**
 * Handles the creation for table `monthly_trading_history_cache`.
 * Has foreign keys to the tables:
 *
 * - `pollet_user`
 */
class m160921_053414_create_monthly_trading_history_cache_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('monthly_trading_history_cache', [
            'id'             => $this->primaryKey(),
            'pollet_user_id' => $this->integer()->notNull(),
            'month'          => $this->string(4)->notNull(),
            'records_json'   => $this->text()->notNull(),
            'updated_at'     => $this->integer()->null(),
            'created_at'     => $this->integer()->null(),
        ]);
        // creates unique index for column `pollet_user_id` and `month`
        $this->createIndex(
            'idx-monthly_trading_history_cache-pollet_user_id-and-month',
            'monthly_trading_history_cache',
            ['pollet_user_id', 'month'],
            true
        );

        // creates index for column `pollet_user_id`
        $this->createIndex(
            'idx-monthly_trading_history_cache-pollet_user_id',
            'monthly_trading_history_cache',
            'pollet_user_id'
        );

        // add foreign key for table `pollet_user`
        $this->addForeignKey(
            'fk-monthly_trading_history_cache-pollet_user_id',
            'monthly_trading_history_cache',
            'pollet_user_id',
            'pollet_user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `pollet_user`
        $this->dropForeignKey(
            'fk-monthly_trading_history_cache-pollet_user_id',
            'monthly_trading_history_cache'
        );

        // drops index for column `pollet_user_id`
        $this->dropIndex(
            'idx-monthly_trading_history_cache-pollet_user_id',
            'monthly_trading_history_cache'
        );

        // drops unique index for column `pollet_user_id` and `month`
        $this->dropIndex(
            'idx-monthly_trading_history_cache-pollet_user_id-and-month',
            'monthly_trading_history_cache'
        );

        $this->dropTable('monthly_trading_history_cache');
    }
}
