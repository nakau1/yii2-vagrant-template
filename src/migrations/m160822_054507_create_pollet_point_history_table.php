<?php

use yii\db\Migration;

/**
 * Handles the creation for table `pollet_point_history`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m160822_054507_create_pollet_point_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('pollet_point_history', [
            'id'                 => $this->primaryKey(),
            'trading_history_id' => $this->bigInteger()->notNull(),
            'pollet_id'          => $this->integer()->notNull(),
            'point'              => $this->decimal(9, 1)->notNull(),
            'title'              => $this->string(50)->notNull(),
            'point_status'       => $this->string(15)->notNull(),
            'raw_data'           => $this->text()->notNull(),
            'trading_date'       => $this->timestamp()->null(),
            'merchant_code'      => $this->string(15)->null(),
            'spent_amount'       => $this->integer()->notNull(),
            'modified_date'      => $this->timestamp()->null(),
            'registered_date'    => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-pollet_point_history-pollet_id',
            'pollet_point_history',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-pollet_point_history-pollet_id',
            'pollet_point_history',
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
            'fk-pollet_point_history-pollet_id',
            'pollet_point_history'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-pollet_point_history-pollet_id',
            'pollet_point_history'
        );

        $this->dropTable('pollet_point_history');
    }
}
