<?php

use yii\db\Migration;

/**
 * Handles the creation for table `charge_source`.
 */
class m160822_032000_create_charge_source_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('charge_source', [
            'id'                 => $this->primaryKey(),
            'charge_source_code' => $this->string(10)->notNull()->unique(),
            'min_value'          => $this->integer()->notNull(),
            'card_issue_fee'     => $this->smallInteger()->notNull()->defaultValue(0),
            'publishing_status'  => $this->string(35)->notNull(),
            'modified_date'      => $this->timestamp()->null(),
            'registered_date'    => $this->timestamp()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('charge_source');
    }
}
