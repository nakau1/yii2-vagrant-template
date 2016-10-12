<?php

use yii\db\Migration;
use app\models\User;
use app\models\UserPlatform;

/**
 * Class m160101_000001_create_tables
 */
class m160101_000001_create_tables extends Migration
{
    const TABLE_OPTION = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id'          => $this->primaryKey()->comment('ユーザID'),
            'name'        => $this->string(256)->null()->comment('ユーザ名'),
            'email'       => $this->string(256)->null()->comment('メールアドレス'),
            'status'      => $this->string(32)->notNull()->comment('ステータス')->defaultValue(User::STATUS_ACTIVE),
            'created_at'  => $this->integer()->null()->comment('作成日時'),
            'updated_at'  => $this->integer()->null()->comment('更新日時'),
        ], self::TABLE_OPTION);

        $this->createTable('user_platform', [
            'user_id'      => $this->primaryKey()->comment('ユーザID'),
            'platform'     => $this->string(10)->notNull()->comment('プラットフォーム')->defaultValue(UserPlatform::WEB),
            'uuid'         => $this->string(256)->null()->comment('UUID'),
            'device_token' => $this->string(256)->null()->comment('デバイストークン'),
            'access_token' => $this->string(256)->null()->comment('アクセストークン'),
        ], self::TABLE_OPTION);

        $this->createTable('user_auth', [
            'user_id'      => $this->primaryKey()->comment('ユーザID'),
            'password'     => $this->string(20)->notNull()->comment('パスワード'),
        ], self::TABLE_OPTION);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_auth');
        $this->dropTable('user_platform');
        $this->dropTable('user');
    }
}
