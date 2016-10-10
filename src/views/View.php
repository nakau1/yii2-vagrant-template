<?php

namespace app\views;

use app\models\PolletUser;
use yii\bootstrap\Html;

class View extends \yii\web\View
{
    const JS_VOID = 'javascript:void(0)';

    /** @var PolletUser 認証されているユーザ */
    public $user;

    /** @var bool 背景をグレーにするかどうか */
    public $isGrayBackground = true;

    /** @var bool ヘッダバーを表示するかどうか */
    public $isShowedHeaderBar = true;

    /** @var bool フッタメニューを表示するかどうか */
    public $isShowedFooterMenu = true;

    /** @var string|null ヘッダバーのタイトル(nullの場合はページタイトルと同じ) */
    public $specifiedTitle = null;

    /**
     * 戻るボタン押下時のリンク先
     * - 指定方法は `Url::to()` と同じ
     * - `null`の場合はデフォルトの戻り先
     * - `false`の場合は戻るボタン非表示
     * @var array|string|bool|null
     */
    public $backAction = null;

    /**
     * コンテンツHTMLタグの追加クラス
     * @var null|string
     */
    public $contentsHtmlClass = null;

    /**
     * 戻るボタンでの戻り先アクションを返す
     * @return array|bool|string|null インデックページのアクション(URL::to()で使用する)
     */
    public function getBackAction()
    {
        if ($this->backAction === false) {
            return null;
        } else if (is_null($this->backAction)) {
            return $this->getDefaultIndexAction();
        } else {
            return $this->backAction;
        }
    }

    /**
     * ユーザステータスに応じてインデックページのアクションを返す
     * @return array インデックページのアクション(URL::to()で使用する)
     */
    private function getDefaultIndexAction()
    {
        switch ($this->user->registration_status) {
            case PolletUser::STATUS_NEW_USER:
                return ['tutorial/'];
            default:
                return ['top/'];
        }
    }

    /**
     * @param $pngName
     * @param array $options
     * @return string
     */
    public function img($pngName, $options = [])
    {
        return Html::img('/img/'. $pngName .'.png', $options);
    }
}
