<?php

use yii\db\Migration;

/**
 * Handles adding flag to table `information`.
 */
class m160912_090127_add_flag_columns_to_information_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('information', 'sends_push',
            $this->smallInteger(1)->notNull()->defaultValue(false)->after('end_date'));
        $this->addColumn('information', 'is_important',
            $this->smallInteger(1)->notNull()->defaultValue(false)->after('sends_push'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('information', 'is_important');
        $this->dropColumn('information', 'sends_push');
    }
}
