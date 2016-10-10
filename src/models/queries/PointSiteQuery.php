<?php

namespace app\models\queries;

use app\models\PointSite;
use app\models\PointSiteToken;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[PointSite]].
 *
 * @see PointSite
 */
class PointSiteQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere([
            PointSite::tableName(). '.publishing_status' => PointSite::PUBLISHING_STATUS_PUBLIC,
        ]);
    }

    public function joinAuthorized()
    {
        $site  = PointSite::tableName();
        $token = PointSiteToken::tableName();

        return $this->select([
            $site . '.*',
            $token . '.id IS NOT NULL AS `isAuthorized`',
        ])->leftJoin(
            $token,
            [
                $token . '.point_site_code' => new Expression($site . '.point_site_code'),
                $token . '.pollet_user_id' => \Yii::$app->user->id,
            ]
        );
    }

    /**
     * @inheritdoc
     * @return PointSite[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PointSite|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
