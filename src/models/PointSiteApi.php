<?php

namespace app\models;

use app\models\queries\PointSiteApiQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "point_site_api".
 *
 * @property integer $id
 * @property string  $point_site_code
 * @property string  $api_name
 * @property string  $url
 * @property string  $publishing_status
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PointSite $pointSite
 */
class PointSiteApi extends ActiveRecord
{
    /** @var string APIの種別…交換 */
    const API_NAME_EXCHANGE = 'exchange';
    /** @var string 公開状態…公開 */
    const PUBLISHING_STATUS_PUBLIC = 'public';
    /** @var string 公開状態…非公開 */
    const PUBLISHING_STATUS_PRIVATE = 'private';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_site_api';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['point_site_code', 'api_name', 'url', 'publishing_status'], 'required'],
            [['updated_at', 'created_at'], 'integer'],
            [['point_site_code'], 'string', 'max' => 10],
            [['api_name'], 'string', 'max' => 30],
            [['url'], 'string', 'max' => 256],
            [['publishing_status'], 'string', 'max' => 35],
            [['point_site_code', 'api_name'], 'unique', 'targetAttribute' => ['point_site_code', 'api_name'], 'message' => 'The combination of Point Site Code and Api Name has already been taken.'],
            [['point_site_code'], 'exist', 'skipOnError' => true, 'targetClass' => PointSite::className(), 'targetAttribute' => ['point_site_code' => 'point_site_code']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'point_site_code'   => '提携サイトコード',
            'api_name'          => 'API名',
            'url'               => 'URL',
            'publishing_status' => '公開状態',
            'updated_at'        => '更新日時',
            'created_at'        => '作成日時',
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
     * @inheritdoc
     * @return PointSiteApiQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PointSiteApiQuery(get_called_class());
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
}
