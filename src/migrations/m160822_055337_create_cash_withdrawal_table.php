<?php

use yii\db\Migration;

/**
 * Handles the creation for table `cash_withdrawal`.
 * Has foreign keys to the tables:
 *
 * - `charge_source`
 */
class m160822_055337_create_cash_withdrawal_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cash_withdrawal', [
            'id'                 => $this->primaryKey(),
            'charge_source_code' => $this->string(10)->notNull(),
            'value'              => $this->integer()->notNull(),
            'modified_date'      => $this->timestamp()->null(),
            'registered_date'    => $this->timestamp()->null(),
        ]);

        // creates index for column `charge_source_code`
        $this->createIndex(
            'idx-cash_withdrawal-charge_source_code',
            'cash_withdrawal',
            'charge_source_code'
        );

        // add foreign key for table `charge_source`
        $this->addForeignKey(
            'fk-cash_withdrawal-charge_source_code',
            'cash_withdrawal',
            'charge_source_code',
            'charge_source',
            'charge_source_code',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `charge_source`
        $this->dropForeignKey(
            'fk-cash_withdrawal-charge_source_code',
            'cash_withdrawal'
        );

        // drops index for column `charge_source_code`
        $this->dropIndex(
            'idx-cash_withdrawal-charge_source_code',
            'cash_withdrawal'
        );

        $this->dropTable('cash_withdrawal');
    }
}
