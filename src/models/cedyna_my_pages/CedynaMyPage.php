<?php

namespace app\models\cedyna_my_pages;

use app\helpers\Selenium;
use app\models\exceptions\CedynaMyPage\ParsingHtmlException;
use app\models\exceptions\CedynaMyPage\NetworkException;
use app\models\exceptions\CedynaMyPage\UnauthorizedException;
use app\models\TradingHistory;
use DateTime;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\WebDriverBy;
use Faker;
use Yii;
use yii\base\Model;

class CedynaMyPage extends Model
{
    /** @var string */
    protected $cedynaId = '';

    /** @var string */
    protected $password = '';

    /** @var Selenium */
    private $selenium;

    /**
     * 設定ファイルを反映したインスタンスを生成する
     *
     * @return CedynaMyPage
     */
    public static function getInstance()
    {
        return Yii::$app->get('cedynaMyPage');
    }

    /**
     * @param array|Selenium $selenium
     */
    public function setSelenium($selenium)
    {
        if ($selenium instanceof Selenium) {
            $this->selenium = $selenium;
        } else {
            $this->selenium = Yii::createObject($selenium);
        }
    }

    /**
     * セディナのカード発行申込みページにメールアドレスを入力し、送信する
     *
     * @param string $email
     * @return bool 送信に成功したかどうか
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function sendIssuingFormLink(string $email): bool
    {
        // TODO: 本物を指定する
        $url = 'http://localhost/demo/cedyna-send-email';
        try {
            $driver = $this->selenium->getDriver();
            $driver->get($url);
            $driver->findElement(WebDriverBy::id('email1'))->sendKeys($email);
            $driver->findElement(WebDriverBy::id('email2'))->sendKeys($email);
            $driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

            return $driver->getTitle() === 'お客様メールアドレス入力完了';
        } catch (WebDriverCurlException $e) {
            // ネットワークがおかしい
            throw new NetworkException($e->getMessage(), $e->getCode(), $e);
        } catch (NoSuchElementException $e) {
            // 取得した HTML が期待通りじゃない
            throw new ParsingHtmlException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * セディナのマイページにログインを施行する。
     *
     * @param string $cedynaId
     * @param string $password
     * @return bool
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function login(string $cedynaId, string $password): bool
    {
        $this->cedynaId = $cedynaId;
        $this->password = $password;

        // TODO: ログイン施行
        $this->selenium->operate($this->cedynaId, $this->password);
        $result = true;

        return $result;
    }

    /**
     * カード残高を取得する。
     *
     * @return int
     * @throws UnauthorizedException
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function cardValue(): int
    {
        // TODO: スクレイピングで取得する
        $this->selenium->operate($this->cedynaId, $this->password);

        $result = Faker\Factory::create()->numberBetween(0, 1000000);

        return $result;
    }

    /**
     * カード利用履歴を取得する。
     *
     * @param string $month 'yymm'の形式の月
     * @return TradingHistory[]
     * @throws UnauthorizedException
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function tradingHistories(string $month): array
    {
        // TODO: スクレイピングで取得する
        $this->selenium->operate($this->cedynaId, $this->password);

        $faker = Faker\Factory::create();
        $beginDate = DateTime::createFromFormat('ym|', $month);
        $endDate = DateTime::createFromFormat('ym|', $month)->modify('+1 month');
        $result = [];
        for ($i = 0; $i < 3; $i++) {
            $result[] = [
                'shop'  => $faker->text(60),
                'value' => $faker->numberBetween(10, 5000),
                'date'  => $faker->dateTimeBetween($beginDate, $endDate)->format('Y/m/d H:i:s'),
            ];
        }

        // 取得結果と結果オブジェクトをマッピング
        $tradingHistories = [];
        foreach ($result as $row) {
            $history = new TradingHistory();
            $history->shop = $row['shop'];
            $history->spent_value = $row['value'];
            $history->trading_date = new DateTime($row['date']);
            $tradingHistories[] = $history;
        }

        return $tradingHistories;
    }
}
