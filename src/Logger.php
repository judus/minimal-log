<?php namespace Maduser\Minimal\Log;

/**
 * Class Logger
 *
 * @package Maduser\Minimal\Log
 */
class Logger
{
    /**
     *
     */
    const DEBUG = 5;
    /**
     *
     */
    const SYSTEM = 4;
    /**
     *
     */
    const INFO = 3;
    /**
     *
     */
    const WARNING = 2;
    /**
     *
     */
    const ERROR = 1;
    /**
     *
     */
    const LEVELS = [null, 'ERROR  ', 'WARNING', 'INFO   ', 'SYSTEM ', 'DEBUG  '];

    /**
     * @var string
     */
    private $dir = '';

    /**
     * @var string|callable
     */
    private $filename;

    /**
     * @var string
     */
    private $fileExt = '.log';

    /**
     * @var int
     */
    private $level = 0;

    /**
     * @var callable
     */
    private $lineFormat;

    /**
     * @var bool
     */
    private $withBenchmarks = false;

    /**
     * @return mixed
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     *
     * @return Logger
     */
    public function setDir(string $dir)
    {
        $this->dir = rtrim($dir, '/') . '/';

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLineFormat()
    {
        return $this->lineFormat;
    }

    /**
     * @param mixed $lineFormat
     *
     * @return Logger
     */
    public function setLineFormat($lineFormat)
    {
        $this->lineFormat = $lineFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        if (is_callable($this->filename)) {
            return $this->filename();
        }

        if ($this->filename) {
            return $this->filename;
        }

        return strftime('%Y-%m-%d') . $this->getFileExt();
    }

    /**
     * @param string|callable $filename
     *
     * @return Logger
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileExt(): string
    {
        return $this->fileExt;
    }

    /**
     * @param string $fileExt
     *
     * @return Logger
     */
    public function setFileExt(string $fileExt): Logger
    {
        $this->fileExt = $fileExt;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return Logger
     */
    public function setLevel(int $level): Logger
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWithBenchmarks(): bool
    {
        return $this->withBenchmarks;
    }

    /**
     * @param bool $withBenchmarks
     *
     * @return Logger
     */
    public function setWithBenchmarks(bool $withBenchmarks): Logger
    {
        $this->withBenchmarks = $withBenchmarks;

        return $this;
    }

    /**
     * Logger constructor.
     *
     * @param string $dir
     * @param int    $logLevel
     * @param bool   $withBenchmarks
     */
    public function __construct(string $dir, int $logLevel = 0, bool $withBenchmarks = false)
    {
        $this->setDir($dir);
        $this->setLevel($logLevel);
        $this->setWithBenchmarks($withBenchmarks);
    }

    /**
     * @param string $msg
     * @param null   $data
     */
    public function debug(string $msg, $data = null)
    {
        $this->log(self::DEBUG, $msg, $data);
    }

    /**
     * @param string $msg
     * @param null   $data
     */
    public function system(string $msg, $data = null)
    {
        $this->log(self::SYSTEM, $msg, $data);
    }

    /**
     * @param string $msg
     * @param null   $data
     */
    public function info(string $msg, $data = null)
    {
        $this->log(self::INFO, $msg, $data);
    }

    /**
     * @param string $msg
     * @param null   $data
     */
    public function warn(string $msg, $data = null)
    {
        $this->log(self::WARNING, $msg, $data);
    }

    /**
     * @param string $msg
     * @param null   $data
     */
    public function error(string $msg, $data = null)
    {
        $this->log(self::ERROR, $msg, $data);
    }

    /**
     * @param int    $level
     * @param string $msg
     * @param null   $data
     */
    public function log(int $level, string $msg, $data = null)
    {
        if ($level <= $this->level) {

            if ($this->isWithBenchmarks()) {
                $msg = $this->interval() . $msg;
            }

            $file = $this->getFile();
            $line = $this->getLine($level, $msg, $data);

            if (file_exists($file)) {
                file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
            } else {
                file_put_contents($file, $line, LOCK_EX);
            }
        }
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->getDir() . $this->getFilename();
    }

    /**
     * @param int    $level
     * @param string $msg
     * @param null   $data
     *
     * @return string
     */
    public function getLine(int $level, string $msg, $data = null)
    {
        if (is_callable($this->lineFormat)) {
            $line = $this->lineFormat;
            return $line($level, $msg, $data);
        }

        return $this->formatLine($level, $msg);
    }

    /**
     * @param int    $level
     * @param string $msg
     *
     * @return string
     */
    public function formatLine(int $level, string $msg)
    {
        return
            strftime('%Y-%m-%d %H:%M:%S')
            . ' | ' . self::LEVELS[$level]
            . ' | ' . $msg
            . PHP_EOL;
    }

    /**
     * @return string
     */
    protected function interval(): string
    {
        if (isset($_SERVER["REQUEST_TIME_FLOAT"])) {
            return microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] . ' - ';
        }

        return '';
    }

}