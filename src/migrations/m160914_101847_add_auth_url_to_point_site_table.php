<?php

use yii\db\Migration;

class m160914_101847_add_auth_url_to_point_site_table extends Migration
{
    public function up()
    {
        $this->addColumn('point_site', 'auth_url', $this->string(256)->notNull()->after('description'));
    }

    public function down()
    {
        $this->dropColumn('point_site', 'auth_url');
    }
}
