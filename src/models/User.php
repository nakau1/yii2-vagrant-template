<?php
namespace app\models;

use app\models\queries\UserQuery;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package app\models
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property UserPlatform $platform
 * @property UserAuth $auth
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE  = 'active';
    const STATUS_REMOVED = 'removed';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'email'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ユーザID',
            'name' => 'ユーザ名',
            'email' => 'メールアドレス',
            'status' => 'ステータス',
            'created_at' => '作成日時',
            'updated_at' => '更新日時',
        ];
    }

    // ===============================================================
    // find
    // ===============================================================

    /**
     * @inheritdoc
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    // ===============================================================
    // relation
    // ===============================================================

    /**
     * @return ActiveQuery
     */
    public function getPlatform()
    {
        return $this->hasOne(UserPlatform::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuth()
    {
        return $this->hasOne(UserAuth::className(), ['user_id' => 'id']);
    }

    // ===============================================================
    // implementation for IdentityInterface
    // ===============================================================

    /**
     * @param int|string $id
     * @return User|array|null
     */
    public static function findIdentity($id)
    {
        return self::find()->andWhere([
            self::tableName() . '.id' => $id,
        ])->one();
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->id == $authKey;
    }
}