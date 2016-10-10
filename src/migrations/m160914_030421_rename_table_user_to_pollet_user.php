<?php

use yii\db\Migration;

class m160914_030421_rename_table_user_to_pollet_user extends Migration
{
    public function up()
    {
        $this->renameTable('user', 'pollet_user');
        $this->renameColumn('pollet_user', 'pollet_id', 'id');
    }

    public function down()
    {
        $this->renameColumn('pollet_user', 'id', 'pollet_id');
        $this->renameTable('pollet_user', 'user');
    }
}
