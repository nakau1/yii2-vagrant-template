<?php
namespace app\controllers;

/**
 * Class DefaultController
 * @package app\controllers
 */
class DefaultController extends CommonController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [

        ]);
    }
}