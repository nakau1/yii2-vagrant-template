<?php

use yii\db\Migration;

/**
 * Handles the creation for table `point_site_token`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `point_site`
 */
class m160822_035115_create_point_site_token_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('point_site_token', [
            'id'              => $this->primaryKey(),
            'pollet_id'       => $this->integer()->notNull(),
            'point_site_code' => $this->string(10)->notNull(),
            'token'           => $this->string(256)->notNull(),
            'modified_date'   => $this->timestamp()->null(),
            'registered_date' => $this->timestamp()->null(),
        ]);
        // creates unique index for column `pollet_id` and `point_site_code`
        $this->createIndex(
            'idx-point_site_token-pollet_id-and-point_site_code',
            'point_site_token',
            ['pollet_id', 'point_site_code'],
            true
        );

        // creates index for column `pollet_id`
        $this->createIndex(
            'idx-point_site_token-pollet_id',
            'point_site_token',
            'pollet_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-point_site_token-pollet_id',
            'point_site_token',
            'pollet_id',
            'user',
            'pollet_id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `point_site_code`
        $this->createIndex(
            'idx-point_site_token-point_site_code',
            'point_site_token',
            'point_site_code'
        );

        // add foreign key for table `point_site`
        $this->addForeignKey(
            'fk-point_site_token-point_site_code',
            'point_site_token',
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
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-point_site_token-pollet_id',
            'point_site_token'
        );

        // drops index for column `pollet_id`
        $this->dropIndex(
            'idx-point_site_token-pollet_id',
            'point_site_token'
        );

        // drops foreign key for table `point_site`
        $this->dropForeignKey(
            'fk-point_site_token-point_site_code',
            'point_site_token'
        );

        // drops index for column `point_site_code`
        $this->dropIndex(
            'idx-point_site_token-point_site_code',
            'point_site_token'
        );

        $this->dropTable('point_site_token');
    }
}
