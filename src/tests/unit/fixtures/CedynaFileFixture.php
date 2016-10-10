<?php
namespace tests\unit\fixtures;

use Faker;
use Yii;
use yii\helpers\FileHelper;
use yii\test\Fixture;

class CedynaFileFixture extends Fixture
{
    private $testTemporaryPath;
    public $csvファイルが入っていないディレクトリ;
    public $csvファイルが1つ入っているディレクトリ;
    public $csvファイルが複数入っているディレクトリ;
    public $csvファイル以外が入っているディレクトリ;
    public $存在しないディレクトリ;
    public $書き込み権限がないディレクトリ;
    public $csvファイルが入っていないディレクトリのcsvファイル = [];
    public $csvファイルが1つ入っているディレクトリのcsvファイル = ['1abc.csv'];
    public $csvファイルが複数入っているディレクトリのcsvファイル = ['2abc.csv', 'def2.csv'];
    public $csvファイル以外が入っているディレクトリのcsvファイル = ['xyz.csv'];
    public $csvファイル以外が入っているディレクトリのcsv以外のファイル = ['xyz.txt', 'csv.log'];

    public $読み取りのテストに使うcsvのパス;
    public $読み取りのテストに使うcsvの内容;

    public function init()
    {
        $this->testTemporaryPath = '/tmp/phpunit';
        $this->csvファイルが入っていないディレクトリ = $this->testTemporaryPath.'/0';
        $this->csvファイルが1つ入っているディレクトリ = $this->testTemporaryPath.'/1';
        $this->csvファイルが複数入っているディレクトリ = $this->testTemporaryPath.'/2';
        $this->csvファイル以外が入っているディレクトリ = $this->testTemporaryPath.'/x';
        $this->存在しないディレクトリ = $this->testTemporaryPath.'/not_exists';
        $this->書き込み権限がないディレクトリ = $this->testTemporaryPath.'/permission_denied';

        $this->読み取りのテストに使うcsvのパス = $this->testTemporaryPath.'/test.csv';
        $this->読み取りのテストに使うcsvの内容 = <<<CSV
"test",123
"abc","def",45
CSV;
    }

    public function load()
    {
        FileHelper::createDirectory($this->csvファイルが入っていないディレクトリ);
        foreach ($this->csvファイルが入っていないディレクトリのcsvファイル as $filename) {
            touch("{$this->csvファイルが入っていないディレクトリ}/{$filename}");
        }
        FileHelper::createDirectory($this->csvファイルが1つ入っているディレクトリ);
        foreach ($this->csvファイルが1つ入っているディレクトリのcsvファイル as $filename) {
            touch("{$this->csvファイルが1つ入っているディレクトリ}/{$filename}");
        }
        FileHelper::createDirectory($this->csvファイルが複数入っているディレクトリ);
        foreach ($this->csvファイルが複数入っているディレクトリのcsvファイル as $filename) {
            touch("{$this->csvファイルが複数入っているディレクトリ}/{$filename}");
        }
        FileHelper::createDirectory($this->csvファイル以外が入っているディレクトリ);
        foreach ($this->csvファイル以外が入っているディレクトリのcsvファイル as $filename) {
            touch("{$this->csvファイル以外が入っているディレクトリ}/{$filename}");
        }
        foreach ($this->csvファイル以外が入っているディレクトリのcsv以外のファイル as $filename) {
            touch("{$this->csvファイル以外が入っているディレクトリ}/{$filename}");
        }
        FileHelper::createDirectory($this->書き込み権限がないディレクトリ, 000);

        $sjisContent = mb_convert_encoding($this->読み取りのテストに使うcsvの内容, 'SJIS');
        file_put_contents($this->読み取りのテストに使うcsvのパス, $sjisContent);
    }

    public function unload()
    {
        // FileHelper は使えない
        if (file_exists($this->書き込み権限がないディレクトリ)) {
            rmdir($this->書き込み権限がないディレクトリ);
        }

        FileHelper::removeDirectory($this->testTemporaryPath);
    }
}
