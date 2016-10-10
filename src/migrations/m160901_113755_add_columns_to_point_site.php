<?php

use yii\db\Migration;

class m160901_113755_add_columns_to_point_site extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('point_site', 'icon_image_url', $this->string(256)->null()->after('url'));
        $this->addColumn('point_site', 'charge_rate', $this->decimal(4, 1)->notNull()->defaultValue(100)->after('url'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('point_site', 'charge_rate');
        $this->dropColumn('point_site', 'icon_image_url');
    }
}
