<?php

use yii\db\Migration;

/**
 * Handles the creation for table `charge_request_history`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `cash_withdrawal`
 */
class m160822_055940_create_charge_request_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('charge_request_history', [
            'id'                 => $this->primaryKey(),
            'pollet_id'          => $this->integer()->notNull(),
            'cash_withdrawal_id' => $this->integer()->notNull(),
            'charge_value'       => $this->integer()->notNull(),
            'cause'              => $this->string(100)->null(),
            'processing_status'  => $this->string(35)->notNull(),
            'modified_date'      => $this->timestamp()->null(),
            'registered_date'    => $this->timestamp()->null(),
        ]);

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-charge_request_history-pollet_id',
            'charge_request_history',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-charge_request_history-pollet_id',
            'charge_request_history',
            'pollet_id',
            'user',
            'pollet_id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `cash_withdrawal_id`
        $this->createIndex(
            'idx-charge_request_history-cash_withdrawal_id',
            'charge_request_history',
            'cash_withdrawal_id'
        );

        // add foreign key for table `cash_withdrawal`
        $this->addForeignKey(
            'fk-charge_request_history-cash_withdrawal_id',
            'charge_request_history',
            'cash_withdrawal_id',
            'cash_withdrawal',
            'id',
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
            'fk-charge_request_history-pollet_id',
            'charge_request_history'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-charge_request_history-pollet_id',
            'charge_request_history'
        );

        // drops foreign key for table `cash_withdrawal`
        $this->dropForeignKey(
            'fk-charge_request_history-cash_withdrawal_id',
            'charge_request_history'
        );

        // drops index for column `cash_withdrawal_id`
        $this->dropIndex(
            'idx-charge_request_history-cash_withdrawal_id',
            'charge_request_history'
        );

        $this->dropTable('charge_request_history');
    }
}
