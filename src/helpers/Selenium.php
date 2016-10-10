<?php

namespace app\helpers;

use app\models\exceptions\CedynaMyPage\ParsingHtmlException;
use app\models\exceptions\CedynaMyPage\NetworkException;
use app\models\exceptions\CedynaMyPage\UnauthorizedException;
use Yii;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Class Selenium
 * @package app\helpers
 */
class Selenium
{
    /**
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return RemoteWebDriver::create(
            'http://localhost:8000/wd/hub',
            DesiredCapabilities::phantomjs()->setCapability(
                'phantomjs.binary.path',
                Yii::$app->basePath.'/../node_modules/phantomjs-prebuilt/bin/phantomjs'
            )
        );
    }

    /**
     * セディナのマイページを操作する
     * FIXME: 仮の実装なので何もしていない
     *
     * @param string $cedynaId
     * @param string $password
     * @throws UnauthorizedException
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function operate(string $cedynaId, string $password)
    {
        if (empty($cedynaId) || empty($password)) {
            // 取得できなかった
            throw new UnauthorizedException();
        }
        $requestResult = true;
        if (!$requestResult) {
            // ネットワークエラー
            throw new NetworkException();
        }
        $parseResult = true;
        if (!$parseResult) {
            // HTML のパースエラー
            throw new ParsingHtmlException();
        }

        // TODO: セディナにリクエストする
    }
}
