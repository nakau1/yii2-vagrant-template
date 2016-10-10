<?php

use yii\db\Migration;

/**
 * Handles the creation for table `point_site`.
 * Has foreign keys to the tables:
 *
 * - `charge_source`
 */
class m160822_032003_create_point_site_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('point_site', [
            'id'              => $this->primaryKey(),
            'point_site_code' => $this->string(10)->notNull()->unique(),
            'site_name'       => $this->string(50)->notNull()->unique(),
            'url'             => $this->string(256)->notNull(),
            'modified_date'   => $this->timestamp()->null(),
            'registered_date' => $this->timestamp()->null(),
        ]);

        // creates index for column `point_site_code`
        $this->createIndex(
            'idx-point_site-point_site_code',
            'point_site',
            'point_site_code'
        );

        // add foreign key for table `charge_sources`
        $this->addForeignKey(
            'fk-point_site-point_site_code',
            'point_site',
            'point_site_code',
            'charge_source',
            'charge_source_code',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `charge_sources`
        $this->dropForeignKey(
            'fk-point_site-point_site_code',
            'point_site'
        );

        // drops index for column `point_site_code`
        $this->dropIndex(
            'idx-point_site-point_site_code',
            'point_site'
        );

        $this->dropTable('point_site');
    }
}
