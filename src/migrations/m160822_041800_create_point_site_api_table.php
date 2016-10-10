<?php

use yii\db\Migration;

/**
 * Handles the creation for table `point_site_api`.
 * Has foreign keys to the tables:
 *
 * - `point_site`
 */
class m160822_041800_create_point_site_api_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('point_site_api', [
            'id'                => $this->primaryKey(),
            'point_site_code'   => $this->string(10)->notNull(),
            'api_name'          => $this->string(30)->notNull(),
            'endpoint'          => $this->string(256)->notNull(),
            'publishing_status' => $this->string(35)->notNull(),
            'modified_date'     => $this->timestamp()->null(),
            'registered_date'   => $this->timestamp()->null(),
        ]);
        // creates unique index for column `point_site_code` and `api_name`
        $this->createIndex(
            'idx-point_site_api-point_site_code-and-api_name',
            'point_site_api',
            ['point_site_code', 'api_name'],
            true
        );

        // creates index for column `point_site_code`
        $this->createIndex(
            'idx-point_site_api-point_site_code',
            'point_site_api',
            'point_site_code'
        );

        // add foreign key for table `point_site`
        $this->addForeignKey(
            'fk-point_site_api-point_site_code',
            'point_site_api',
            'point_site_code',
            'point_site',
            'point_site_code',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `point_site`
        $this->dropForeignKey(
            'fk-point_site_api-point_site_code',
            'point_site_api'
        );

        // drops index for column `point_site_code`
        $this->dropIndex(
            'idx-point_site_api-point_site_code',
            'point_site_api'
        );

        $this->dropTable('point_site_api');
    }
}
