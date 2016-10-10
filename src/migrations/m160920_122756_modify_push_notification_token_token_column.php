<?php

use app\models\PushNotificationToken;
use yii\db\Migration;

/**
 * トークン保存ColumnをTEXTに変更
 * Class m160920_122756_modify_push_notification_token_token_column
 */
class m160920_122756_modify_push_notification_token_token_column extends Migration
{
    public function up()
    {
        $this->alterColumn(PushNotificationToken::tableName(), 'token', $this->text()->notNull());
    }

    public function down()
    {
        $this->alterColumn(PushNotificationToken::tableName(), 'token', $this->string(256)->notNull());
    }
}
