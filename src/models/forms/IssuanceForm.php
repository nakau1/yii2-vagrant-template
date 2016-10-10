<?php

namespace app\models\forms;

use app\models\cedyna_my_pages\CedynaMyPage;
use app\models\traits\ValidateTrait;
use Yii;
use yii\base\Model;

/**
 * カード発行手続入力フォーム用モデル
 * @package app\models\forms
 */
class IssuanceForm extends Model
{
    use ValidateTrait;

    public $mail_address;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['mail_address'], 'required'],
            [['mail_address'], 'validateLaxEmail'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'mail_address' => 'メールアドレス',
        ];
    }

    /**
     * 認証メールを送信する
     * @return bool
     */
    public function send()
    {
        if (!$this->validate()) {
            return false;
        }

        return CedynaMyPage::getInstance()->sendIssuingFormLink($this->mail_address);
    }

    /***
     * required implementation of ValidateTrait
     * @return $this
     */
    protected function getModel()
    {
        return $this;
    }
}
