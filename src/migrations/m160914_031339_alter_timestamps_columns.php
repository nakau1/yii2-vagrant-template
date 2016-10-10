<?php

use yii\db\Migration;

class m160914_031339_alter_timestamps_columns extends Migration
{
    private $tables = [
        'card_value_cache',
        'cash_withdrawal',
        'charge_error_history',
        'charge_request_history',
        'charge_source',
        'information',
        'inquiry',
        'point_site',
        'point_site_api',
        'point_site_token',
        'pollet_point_history',
        'pollet_user',
        'push_notification_token',
        'register_campaign_point_percentage',
        'trading_history_cache',
    ];
    private $insertOnlyTables = [
        'push_information_opening',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            $this->renameColumn($table, 'registered_date', 'created_at');
            $this->renameColumn($table, 'modified_date', 'updated_at');
            $this->alterColumn($table, 'created_at', $this->integer()->null());
            $this->alterColumn($table, 'updated_at', $this->integer()->null());
        }
        foreach ($this->insertOnlyTables as $table) {
            $this->renameColumn($table, 'registered_date', 'created_at');
            $this->alterColumn($table, 'created_at', $this->integer()->null());
        }
    }

    public function down()
    {
        foreach ($this->insertOnlyTables as $table) {
            $this->alterColumn($table, 'created_at', $this->timestamp()->null());
            $this->renameColumn($table, 'created_at', 'registered_date');
        }
        foreach ($this->tables as $table) {
            $this->alterColumn($table, 'updated_at', $this->timestamp()->null());
            $this->alterColumn($table, 'created_at', $this->timestamp()->null());
            $this->renameColumn($table, 'updated_at', 'modified_date');
            $this->renameColumn($table, 'created_at', 'registered_date');
        }
    }
}
