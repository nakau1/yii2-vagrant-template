<?php

use yii\db\Migration;

class m160912_092226_alter_pollet_point_history_table extends Migration
{
    public function up()
    {
        $this->dropColumn('pollet_point_history', 'point_status');
        $this->addColumn('pollet_point_history', 'point_rate_percentage', $this->double()->notNull());
        $this->alterColumn('pollet_point_history', 'title', $this->string(60)->notNull());
        $this->renameColumn('pollet_point_history', 'spent_amount', 'spent_value');
    }

    public function down()
    {
        $this->renameColumn('pollet_point_history', 'spent_value', 'spent_amount');
        $this->alterColumn('pollet_point_history', 'title', $this->string(50)->notNull());
        $this->dropColumn('pollet_point_history', 'point_rate_percentage');
        $this->addColumn('pollet_point_history', 'point_status', $this->string(15)->notNull());
    }
}
