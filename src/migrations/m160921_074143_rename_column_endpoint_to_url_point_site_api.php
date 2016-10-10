<?php

use yii\db\Migration;

class m160921_074143_rename_column_endpoint_to_url_point_site_api extends Migration
{
    public function up()
    {
        $this->renameColumn('point_site_api', 'endpoint', 'url');
    }

    public function down()
    {
        $this->renameColumn('point_site_api', 'url', 'endpoint');
    }
}
