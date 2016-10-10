<?php

use yii\db\Migration;

class m160912_075647_add_columns_for_introduction_to_point_site extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('point_site', 'charge_rate');
        $this->addColumn('point_site', 'denomination',
            $this->string(16)->notNull()->defaultValue('pt')->after('icon_image_url'));
        $this->addColumn('point_site', 'introduce_charge_rate_point',
            $this->integer()->notNull()->after('denomination'));
        $this->addColumn('point_site', 'introduce_charge_rate_price',
            $this->integer()->notNull()->after('introduce_charge_rate_point'));
        $this->addColumn('point_site', 'description',
            $this->text()->notNull()->after('introduce_charge_rate_price'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('point_site', 'description');
        $this->dropColumn('point_site', 'introduce_charge_rate_price');
        $this->dropColumn('point_site', 'introduce_charge_rate_point');
        $this->dropColumn('point_site', 'denomination');
        $this->addColumn('point_site', 'charge_rate', $this->decimal(4, 1)->notNull()->defaultValue(100)->after('url'));
    }
}
