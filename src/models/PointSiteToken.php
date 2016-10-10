<?php

namespace app\models;

use app\models\queries\PointSiteTokenQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "point_site_token".
 *
 * @property integer $id
 * @property integer $pollet_user_id
 * @property string $point_site_code
 * @property string $token
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PointSite $pointSite
 * @property PolletUser $polletUser
 */
class PointSiteToken extends ActiveRecord
{
    const PUBLISHING_STATUS_PUBLIC = 'public';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_site_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pollet_user_id', 'point_site_code', 'token'], 'required'],
            [['pollet_user_id', 'updated_at', 'created_at'], 'integer'],
            [['point_site_code'], 'string', 'max' => 10],
            [['token'], 'string', 'max' => 256],
            [['pollet_user_id', 'point_site_code'], 'unique', 'targetAttribute' => ['pollet_user_id', 'point_site_code'], 'message' => 'The combination of Pollet User ID and Point Site Code has already been taken.'],
            [['point_site_code'], 'exist', 'skipOnError' => true, 'targetClass' => PointSite::className(), 'targetAttribute' => ['point_site_code' => 'point_site_code']],
            [['pollet_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => PolletUser::className(), 'targetAttribute' => ['pollet_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'pollet_user_id'  => 'ポレットユーザID',
            'point_site_code' => '提携サイトコード',
            'token'           => 'トークン',
            'updated_at'      => '更新日時',
            'created_at'      => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPointSite()
    {
        return $this->hasOne(PointSite::className(), ['point_site_code' => 'point_site_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPolletUser()
    {
        return $this->hasOne(PolletUser::className(), ['id' => 'pollet_user_id']);
    }

    /**
     * @inheritdoc
     * @return PointSiteTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PointSiteTokenQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 指定したユーザと紐づく提携サイト連携レコードを追加する
     * @param $polletUserID integer ポレットユーザID
     * @param $token string トークン
     * @param $pointSiteCode string 提携サイトコード
     * @return bool 成功 / 失敗
     */
    public function add($polletUserID, $token, $pointSiteCode)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->pollet_user_id  = $polletUserID;
            $this->token           = $token;
            $this->point_site_code = $pointSiteCode;

            if (!$this->save()) {
                throw new \Exception('failed add token.');
            }
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }
}
