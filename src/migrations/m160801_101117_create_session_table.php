<?php

use yii\db\Migration;

/**
 * Handles the creation for table `session`.
 */
class m160801_101117_create_session_table extends Migration
{
    public function init()
    {
        $this->db = 'sessionDb';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('session', [
            'id' => $this->char(40),
            'expire' => $this->integer(),
            'data' => $this->binary(),
            'PRIMARY KEY(id)',
        ]);
        $this->createIndex('index-expire', 'session', 'expire');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('session');
    }
}
