<?php

namespace app\models;

use app\models\point_site_cooperation\PointSiteCooperation;
use app\models\queries\PointSiteQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "point_site".
 *
 * @property integer $id
 * @property string  $point_site_code
 * @property string  $site_name
 * @property string  $url
 * @property string  $icon_image_url
 * @property string  $denomination ポイントの単位
 * @property integer $introduce_charge_rate_point 紹介時交換レート表示値（ポイント）
 * @property integer $introduce_charge_rate_price 紹介時交換レート表示値（現金）
 * @property string  $description
 * @property string  $auth_url 要認証時に飛ばす先のURL
 * @property string  $publishing_status
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property ChargeSource     $chargeSource
 * @property PointSiteApi[]   $apis
 * @property PointSiteToken[] $tokens
 * @property PolletUser[]     $polletUsers
 *
 * @property integer $myValidPoint:
 */
class PointSite extends ActiveRecord
{
    /** @var string 公開状態…公開 */
    const PUBLISHING_STATUS_PUBLIC = 'public';
    /** @var string 公開状態…非公開*/
    const PUBLISHING_STATUS_PRIVATE = 'private';

    /** @var bool 認証されているかどうか */
    public $isAuthorized = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_site';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['point_site_code', 'site_name', 'url', 'introduce_charge_rate_point', 'introduce_charge_rate_price', 'description', 'auth_url', 'publishing_status'], 'required'],
            [['introduce_charge_rate_point', 'introduce_charge_rate_price', 'updated_at', 'created_at'], 'integer'],
            [['description'], 'string'],
            [['point_site_code'], 'string', 'max' => 10],
            [['site_name'], 'string', 'max' => 50],
            [['url', 'icon_image_url', 'auth_url'], 'string', 'max' => 256],
            [['denomination'], 'string', 'max' => 16],
            [['publishing_status'], 'string', 'max' => 35],
            [['point_site_code'], 'unique'],
            [['site_name'], 'unique'],
            [['point_site_code'], 'exist', 'skipOnError' => true, 'targetClass' => ChargeSource::className(), 'targetAttribute' => ['point_site_code' => 'charge_source_code']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                          => 'ID',
            'point_site_code'             => '提携サイトコード',
            'site_name'                   => '表示用サイト名',
            'url'                         => 'サイトURL',
            'icon_image_url'              => 'アイコン画像URL',
            'denomination'                => 'ポイント単位',
            'introduce_charge_rate_point' => '紹介時交換レート表示値（ポイント）',
            'introduce_charge_rate_price' => '紹介時交換レート表示値（現金）',
            'description'                 => 'サイトの説明',
            'auth_url'                    => '認証URL',
            'publishing_status'           => '公開状態',
            'updated_at'                  => '更新日時',
            'created_at'                  => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getChargeSource()
    {
        return $this->hasOne(ChargeSource::className(), ['charge_source_code' => 'point_site_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getApis()
    {
        return $this->hasMany(PointSiteApi::className(), ['point_site_code' => 'point_site_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(PointSiteToken::className(), ['point_site_code' => 'point_site_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPolletUsers()
    {
        return $this->hasMany(PolletUser::className(), ['id' => 'pollet_user_id'])->viaTable('point_site_token', ['point_site_code' => 'point_site_code']);
    }

    /**
     * @inheritdoc
     * @return PointSiteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PointSiteQuery(get_called_class());
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

    // ===============================================================
    // dynamic getter properties
    // ===============================================================

    /**
     * ポイントサイトのポイント残高を返す
     * @return int
     */
    public function getMyValidPoint()
    {
        /** @var PolletUser $user */
        $user = \Yii::$app->user->identity;
        if (!$user->isActivatedUser()) {
            return 0;
        }
        return PointSiteCooperation::fetchValidPointAsCash($this->point_site_code, $user->id);
    }
}
