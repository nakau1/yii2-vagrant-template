<?php

namespace tests\unit\models\cedyna_files;

use app\models\cedyna_files\CedynaFile;
use app\models\exceptions\CedynaFile\FileNotExistsException;
use Faker;
use RuntimeException;
use tests\unit\fixtures\CedynaFileFixture;
use yii\codeception\TestCase;

class CedynaFileTest extends TestCase
{
    public $appConfig = '@app/config/console.php';

    public function setUp()
    {
        parent::setUp();
    }

    public function fixtures()
    {
        return [
            'fixture' => CedynaFileFixture::class,
        ];
    }

    /**
     * @test
     * @dataProvider ディレクトリ名と存在するcsvファイル
     *
     * @param array $expectFileNames
     * @param string $directory
     */
    public function ディレクトリに存在するcsvファイルをすべて取得できる(array $expectFileNames, string $directory)
    {
        $findFiles = CedynaFile::findAll($directory);
        $findFileNames = array_map(function (CedynaFile $file) {
            return $file->getName();
        }, $findFiles);

        foreach ($expectFileNames as $fileName) {
            $this->assertContains($fileName, $findFileNames);
        }
    }

    /**
     * @return array
     */
    public function ディレクトリ名と存在するcsvファイル()
    {
        return [
            [
                $this->fixture->csvファイルが入っていないディレクトリのcsvファイル,
                $this->fixture->csvファイルが入っていないディレクトリ,
            ],
            [
                $this->fixture->csvファイルが1つ入っているディレクトリのcsvファイル,
                $this->fixture->csvファイルが1つ入っているディレクトリ,
            ],
            [
                $this->fixture->csvファイルが複数入っているディレクトリのcsvファイル,
                $this->fixture->csvファイルが複数入っているディレクトリ,
            ],
            [
                $this->fixture->csvファイル以外が入っているディレクトリのcsvファイル,
                $this->fixture->csvファイル以外が入っているディレクトリ,
            ],
        ];
    }

    /**
     * @test
     */
    public function ディレクトリに存在するcsvファイル以外を取得しない()
    {
        $findFiles = CedynaFile::findAll($this->fixture->csvファイル以外が入っているディレクトリ);
        $findFileNames = array_map(function (CedynaFile $file) {
            return $file->getName();
        }, $findFiles);

        foreach ($this->fixture->csvファイル以外が入っているディレクトリのcsv以外のファイル as $fileName) {
            $this->assertNotContains($fileName, $findFileNames);
        }
    }

    /**
     * @test
     */
    public function ファイルの移動ができる()
    {
        $srcDirectory = $this->fixture->csvファイルが1つ入っているディレクトリ;
        $dstDirectory = $this->fixture->csvファイルが入っていないディレクトリ;
        $filename = $this->fixture->csvファイルが1つ入っているディレクトリのcsvファイル[0];
        $originalContent = file_get_contents("{$srcDirectory}/{$filename}");

        $cedynaFile = new CedynaFile("{$srcDirectory}/{$filename}");
        $cedynaFile->moveTo($dstDirectory);

        // 移動先にファイルができている
        $this->assertFileExists("{$dstDirectory}/{$filename}");
        // 移動元のファイルがなくなっている
        $this->assertFileNotExists("{$srcDirectory}/{$filename}");
        // 移動後の内容が移動前と同一である
        $this->assertEquals($originalContent, file_get_contents("{$dstDirectory}/{$filename}"));
    }

    /**
     * @test
     * @dataProvider 書き込みできないディレクトリ
     * @param string $dstDirectory
     */
    public function 移動先にファイルが書き込めない場合例外が発生する(string $dstDirectory)
    {
        $srcDirectory = $this->fixture->csvファイルが1つ入っているディレクトリ;
        $filename = $this->fixture->csvファイルが1つ入っているディレクトリのcsvファイル[0];

        $cedynaFile = new CedynaFile("{$srcDirectory}/{$filename}");
        $this->expectException(RuntimeException::class);
        $cedynaFile->moveTo($dstDirectory);
    }

    /**
     * @test
     * @dataProvider 書き込みできないディレクトリ
     * @param string $dstDirectory
     */
    public function 移動先にファイルが書き込めない場合移動元のファイルが消えない(string $dstDirectory)
    {
        $srcDirectory = $this->fixture->csvファイルが1つ入っているディレクトリ;
        $filename = $this->fixture->csvファイルが1つ入っているディレクトリのcsvファイル[0];
        $originalContent = file_get_contents("{$srcDirectory}/{$filename}");

        $cedynaFile = new CedynaFile("{$srcDirectory}/{$filename}");

        try {
            $cedynaFile->moveTo($dstDirectory);
            $this->fail();
        } catch (RuntimeException $ignored) {
        }

        // 移動元のファイルが消えない
        $this->assertFileExists("{$srcDirectory}/{$filename}");
        // 処理後の内容が処理前と同一である
        $this->assertEquals($originalContent, file_get_contents("{$srcDirectory}/{$filename}"));
    }

    /**
     * @return array
     */
    public function 書き込みできないディレクトリ()
    {
        return [
            '存在しないディレクトリ'                   => [$this->fixture->存在しないディレクトリ],
            '書き込み権限がないディレクトリ（rootユーザーだと失敗）' => [$this->fixture->書き込み権限がないディレクトリ],
        ];
    }

    /**
     * @test
     */
    public function csvファイルを読み取ることができる()
    {
        $file = new CedynaFile($this->fixture->読み取りのテストに使うcsvのパス);
        $lines = [];

        foreach ($file->readLinesAll() as $line) {
            $lines[] = $line;
        }

        // 文字列のダブルクォートが取り除かれていること
        $this->assertEquals('test', $lines[0][0]);
        // 数値を読み取ることができる
        $this->assertEquals(123, $lines[0][1]);
        // 2行目以降を読み取ることができる
        $this->assertEquals(['abc', 'def', 45], $lines[1]);
    }

    /**
     * @test
     */
    public function 存在しないファイルを読み取ろうとした場合例外が発生する()
    {
        $file = new CedynaFile("{$this->fixture->csvファイルが入っていないディレクトリ}/not_exists.csv");
        $this->expectException(FileNotExistsException::class);
        foreach ($file->readLinesAll() as $ignored) {
        };
    }
}