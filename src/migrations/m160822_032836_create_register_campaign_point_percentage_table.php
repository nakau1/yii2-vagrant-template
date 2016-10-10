<?php

use yii\db\Migration;

/**
 * Handles the creation for table `register_campaign_point_percentage`.
 */
class m160822_032836_create_register_campaign_point_percentage_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('register_campaign_point_percentage', [
            'id'                => $this->primaryKey(),
            'period'            => $this->integer()->notNull(),
            'point_rate'        => $this->decimal(4, 1)->notNull(),
            'begin_date'        => $this->timestamp()->null(),
            'end_date'          => $this->timestamp()->null(),
            'publishing_status' => $this->string(35)->notNull(),
            'modified_date'     => $this->timestamp()->null(),
            'registered_date'   => $this->timestamp()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('register_campaign_point_percentage');
    }
}
