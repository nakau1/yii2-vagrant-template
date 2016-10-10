<?php

namespace app\controllers;

use app\models\forms\SignInForm;
use app\models\PolletUser;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthController extends CommonController
{
    /**
     * 16. ログイン
     * @return string|Response
     */
    public function actionSignIn()
    {
        $failed = !is_null(Yii::$app->session->get(self::SESSION_FAILED_KEY));
        $signInForm = new SignInForm();

        $signInForm->scenario = (!$this->authorizedUser->isSignOut() || $failed) ?
            SignInForm::SCENARIO_ID_WITH_PW :
            SignInForm::SCENARIO_ID_ONLY;
        Yii::$app->session->remove(self::SESSION_FAILED_KEY);

        if ($signInForm->load(Yii::$app->request->post())) {
            if ($signInForm->authenticate($this->authorizedUser)) {
                /** @var $user PolletUser */
                $user = Yii::$app->user->identity;
                $user->updateStatus(PolletUser::STATUS_ACTIVATED);
                return $this->goHome();
            } else {
                Yii::$app->session->set(self::SESSION_FAILED_KEY, '1');
                $signInForm->scenario = SignInForm::SCENARIO_ID_WITH_PW;
            }
        }

        return $this->render('sign-in', [
            'signInForm' => $signInForm,
        ]);
    }

    /**
     * ログアウト
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSignOut()
    {
        if ($this->authorizedUser->isActivatedUser()) {
            $this->authorizedUser->updateStatus(PolletUser::STATUS_SIGN_OUT);
            return $this->goHome();
        } else {
            throw new NotFoundHttpException();
        }
    }
}
