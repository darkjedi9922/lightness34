<?php namespace frame\tools;

use frame\tools\files\File;
use frame\tools\Client;
use frame\lists\base\FileLineList;

class Logger
{
    const EMERGENCY = 'Emergency';
    const ALERT     = 'Alert';
    const CRITICAL  = 'Critical';
    const ERROR     = 'Error';
    const WARNING   = 'Warning';
    const NOTICE    = 'Notice';
    const INFO      = 'Info';
    const DEBUG     = 'Debug';
    const TESTING   = 'Testing';

    private $handle = null;
    private $filename = null;

    public function __construct($filename)
    {
        if (!File::exists($filename)) File::createFullPath($filename);
        // Почему открывается в бинарном режиме, см. write()
        $this->handle = fopen($filename, 'ab');
        $this->filename = $filename;
    }

    public function write(string $type, string $message)
    {
        $date = date('d.m.Y H:i');
        $ip = Client::isCli() ? 'CLI' : Client::getIp();
        $text = "[$date - $ip] $type: $message" . PHP_EOL;

        /**
         * @see https://www.php.net/fwrite
         * В системах, различающих двоичные и текстовые файлы (к примеру, Windows), 
         * файл должен быть открыт используя флаг 'b' в конце аргумента mode функции
         * fopen().
         * 
         * В данном случае, без него, PHP_EOL на Windows почему-то при записи
         * превращается из "\r\n" в "\r\r\n" о_О
         */
        fwrite($this->handle, $text);
    }

    /**
     * Считывает и парсит записи лога и возвращает их в удобном виде с помощью
     * массива ассоциативных массивов вида [
     *  'date' => '01.01.2020 12:42',
     *  'ip' => 'CLI',
     *  'type' => 'Testing',
     *  'message' => "Some\nmessage\nwith\several\nlines"
     * ]
     */
    public function read(): array
    {
        $result = [];
        $current = [];
        $lines = new FileLineList($this->filename);
        foreach ($lines as $line) {
            $isHeader = !empty($line) && $line[0] === '[';
            if ($isHeader) {
                if (!empty($current)) {
                    $current['message'] = trim($current['message']);
                    $result[] = $current;
                    $current = [];
                }
                $current['date'] = explode(' -', explode('[', $line, 2)[1], 2)[0];
                $current['ip'] = explode('- ', explode(']', $line, 2)[0], 2)[1];
                $current['type'] = explode('] ', explode(': ', $line, 2)[0], 2)[1];
                $current['message'] = explode(': ', $line, 2)[1];
            } else {
                $current['message'] .= $line;
            }
        }

        if (!empty($current)) {
            $current['message'] = trim($current['message']);
            $result[] = $current;
        }
        return $result;
    }

    public function getFile(): string
    {
        return $this->filename;
    }
}