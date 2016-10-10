<?php

use yii\db\Migration;

class m160914_073202_rename_columns_pollet_id_to_pollet_user_id extends Migration
{
    private $childrenTables = [
        'card_value_cache',
        'charge_request_history',
        'inquiry',
        'point_site_token',
        'pollet_point_history',
        'push_information_opening',
        'push_notification_token',
        'trading_history_cache',
    ];

    public function up()
    {
        foreach ($this->childrenTables as $table) {
            $this->renameColumn($table, 'pollet_id', 'pollet_user_id');
        }
    }

    public function down()
    {
        foreach ($this->childrenTables as $table) {
            $this->renameColumn($table, 'pollet_user_id', 'pollet_id');
        }
    }
}
