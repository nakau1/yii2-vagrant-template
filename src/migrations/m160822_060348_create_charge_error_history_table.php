<?php

use yii\db\Migration;

/**
 * Handles the creation for table `charge_error_history`.
 * Has foreign keys to the tables:
 *
 * - `charge_request_history`
 */
class m160822_060348_create_charge_error_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('charge_error_history', [
            'id'                        => $this->primaryKey(),
            'charge_request_history_id' => $this->integer()->notNull(),
            'error_code'                => $this->string(20)->notNull(),
            'raw_data'                  => $this->text()->notNull(),
            'modified_date'             => $this->timestamp()->null(),
            'registered_date'           => $this->timestamp()->null(),
        ]);

        // creates index for column `charge_request_history_id`
        $this->createIndex(
            'idx-charge_error_history-charge_request_history_id',
            'charge_error_history',
            'charge_request_history_id'
        );

        // add foreign key for table `charge_request_history`
        $this->addForeignKey(
            'fk-charge_error_history-charge_request_history_id',
            'charge_error_history',
            'charge_request_history_id',
            'charge_request_history',
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
        // drops foreign key for table `charge_request_history`
        $this->dropForeignKey(
            'fk-charge_error_history-charge_request_history_id',
            'charge_error_history'
        );

        // drops index for column `charge_request_history_id`
        $this->dropIndex(
            'idx-charge_error_history-charge_request_history_id',
            'charge_error_history'
        );

        $this->dropTable('charge_error_history');
    }
}
