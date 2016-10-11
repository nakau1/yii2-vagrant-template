<?php
namespace app\views;

use app\models\User;

/**
 * Class View
 * @package app\views
 */
class View extends \yii\web\View
{
    const JS_VOID = 'javascript:void(0)';

    /** @var User 認証されているユーザ */
    public $user;
}