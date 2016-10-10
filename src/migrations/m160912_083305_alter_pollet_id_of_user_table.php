<?php

use yii\db\Migration;

class m160912_083305_alter_pollet_id_of_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'user_code_secret', $this->string(64)->notNull()->unique()->after('pollet_id')->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('user', 'user_code_secret');
    }
}
