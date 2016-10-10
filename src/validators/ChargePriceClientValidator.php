<?php

namespace app\validators;

use app\helpers\Format;
use app\models\forms\ChargePriceForm;
use yii\validators\Validator;

/**
 * Class ChargePriceClientValidator
 * @package app\validators
 */
class ChargePriceClientValidator extends Validator
{
    /**
     * @param $model ChargePriceForm
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        if ($model->price < $model->minValue) {
            $this->addError($model, $attribute, $this->getMessageLessMinValue($model->minValue));
        }

        if ($model->price > $model->maxValue) {
            $this->addError($model, $attribute, $this->getMessageOverMaxValue($model->maxValue));
        }
    }

    /**
     * @param $model ChargePriceForm
     * @param $attribute
     * @param $view
     * @return string
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $conditions = [
            'value < '. $model->minValue => $this->getMessageLessMinValue($model->minValue),
            'value > '. $model->maxValue => $this->getMessageOverMaxValue($model->maxValue),
        ];

        $ret = '';
        foreach ($conditions as $condition => $errorMessage) {
            $ret .= 'if ('. $condition .') {';
            $ret .= 'messages.push("'. $errorMessage .'");';
            $ret .= '}';
        }
        return $ret;
    }

    /**
     * @param $minValue integer
     * @return string
     */
    private function getMessageLessMinValue($minValue) {
        return 'チャージ金額は '. Format::formattedJapaneseCurrency($minValue) . ' を下回ってはいけません';
    }

    /**
     * @param $maxValue integer
     * @return string
     */
    private function getMessageOverMaxValue($maxValue) {
        return 'チャージ金額は '. Format::formattedJapaneseCurrency($maxValue) . ' を上回ってはいけません';
    }
}
