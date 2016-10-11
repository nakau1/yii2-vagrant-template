<?php
namespace tests\unit\fixtures;

use yii\db\ActiveRecord;
use yii\test\Fixture;

class PolletDbFixture extends Fixture
{
    public function unload()
    {
        foreach (ActiveRecord::getDb()->getSchema()->tableSchemas as $table) {
            if ($table->fullName === 'migration') {
                // 消してしまうとマイグレーションが失敗する
                continue;
            }

            ActiveRecord::getDb()->createCommand()->delete($table->fullName)->execute();
            if ($table->sequenceName !== null) {
                ActiveRecord::getDb()->createCommand()->resetSequence($table->fullName, 1)->execute();
            }
        }
    }
}
