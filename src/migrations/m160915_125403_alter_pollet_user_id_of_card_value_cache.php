<?php

use yii\db\Migration;

class m160915_125403_alter_pollet_user_id_of_card_value_cache extends Migration
{
    public function up()
    {
        $this->alterColumn('card_value_cache', 'pollet_user_id', $this->integer()->notNull()->unique());
    }

    public function down()
    {
        $this->alterColumn('card_value_cache', 'pollet_user_id', $this->integer()->notNull());
    }
}
