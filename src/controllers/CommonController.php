<?php
namespace app\controllers;

use app\models\User;
use app\views\View;
use Yii;
use yii\web\Controller;

/**
 * Class CommonController
 * @package app\controllers
 */
class CommonController extends Controller
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->user = Yii::$app->user->identity;
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = [])
    {
        /** @var $viewObject View */
        $viewObject = $this->view;
        $viewObject->user = $this->user;

        return parent::render($view, $params);
    }
}