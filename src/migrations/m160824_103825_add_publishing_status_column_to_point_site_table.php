<?php

use yii\db\Migration;

/**
 * Handles adding publishing_status to table `point_site`.
 */
class m160824_103825_add_publishing_status_column_to_point_site_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('point_site', 'publishing_status', $this->string(35)->notNull()->after('url'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('point_site', 'publishing_status');
    }
}
