<?php

use yii\db\Migration;

/**
 * Handles the creation for table `information`.
 */
class m160822_032818_create_information_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('information', [
            'id'                => $this->primaryKey(),
            'heading'           => $this->string(50)->notNull(),
            'body'              => $this->text()->notNull(),
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
        $this->dropTable('information');
    }
}
