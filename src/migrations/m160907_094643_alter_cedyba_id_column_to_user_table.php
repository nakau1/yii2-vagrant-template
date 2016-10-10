<?php

use yii\db\Migration;

class m160907_094643_alter_cedyba_id_column_to_user_table extends Migration
{
    public function up()
    {
        $this->alterColumn('user','cedyna_id','BIGINT(16) UNSIGNED ZEROFILL NULL UNIQUE');
    }

    public function down()
    {
        $this->alterColumn('user','cedyna_id',$this->integer(20)->null()->unique());
    }
}
