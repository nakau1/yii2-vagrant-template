<?php

namespace app\models\cedyna_files;

use app\models\exceptions\CedynaFile\DirectoryNotWritableException;
use app\models\exceptions\CedynaFile\FileAlreadyExistsException;
use app\models\exceptions\CedynaFile\FileNotExistsException;
use Generator;
use SplFileObject;
use Yii;
use yii\helpers\FileHelper;

class CedynaFile
{
    private $filePath;
    private $saveContent;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->saveContent = null;
    }

    /**
     * @param string $directory
     *
     * @return CedynaFile[]
     */
    public static function findAll(string $directory): array
    {
        $filePaths = FileHelper::findFiles($directory, ['only' => ['*.csv']]);
        $files = [];
        foreach ($filePaths as $filePath) {
            $files[] = new static($filePath);
        }

        return $files;
    }

    /**
     * @return Generator
     *
     * @throws FileNotExistsException
     */
    public function readLinesAll(): Generator
    {
        if (!file_exists($this->filePath)) {
            throw new FileNotExistsException('ファイルが存在しません');
        }

        $csv = new SplFileObject($this->filePath);
        $csv->setFlags(SplFileObject::READ_CSV);
        foreach ($csv as $line) {
            yield $line;
        }
    }

    /**
     * @return Generator
     *
     * @throws FileNotExistsException
     */
    public function readDataLinesAll(): Generator
    {
        foreach ($this->readLinesAll() as $line) {
            if ($line[0] === 'D') {
                yield $line;
            }
        }
    }

    /**
     * @param string $dstDirectory
     *
     * @throws DirectoryNotWritableException
     * @throws FileAlreadyExistsException
     */
    public function moveTo(string $dstDirectory)
    {
        if (!is_writable($dstDirectory)) {
            throw new DirectoryNotWritableException('移動先のディレクトリに書き込みできません');
        }
        $dstFilePath = $dstDirectory.'/'.$this->getName();
        if (file_exists($dstFilePath)) {
            throw new FileAlreadyExistsException('移動先にファイルが存在します');
        }

        rename($this->filePath, $dstFilePath);
        $this->filePath = $dstFilePath;
    }

    /**
     * @param string $dstDirectory
     */
    public function copyTo(string $dstDirectory)
    {
        $dstFilePath = $dstDirectory.'/'.$this->getName();
        copy($this->filePath, $dstFilePath);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return basename($this->filePath);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $content
     * @return CedynaFile
     */
    public function setSaveContent(string $content)
    {
        $this->saveContent = $content;

        return $this;
    }

    /**
     * @param bool $fileAppend 追記する場合true,上書きする場合false
     * @return bool
     */
    public function save($fileAppend = false): bool
    {
        if ($this->saveContent === null) {
            return false;
        }

        $saveContent = mb_convert_encoding($this->saveContent, 'SJIS');
        if ($fileAppend) {
            $result = file_put_contents($this->filePath, $saveContent, FILE_APPEND);
        } else {
            $result = file_put_contents($this->filePath, $saveContent);
        }

        return $result !== false;
    }
}
