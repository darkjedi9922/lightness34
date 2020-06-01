<?php namespace engine\admin\logging;

use frame\lists\paged\PagedList;
use frame\config\ConfigRouter;
use frame\tools\Logger;
use frame\errors\NotSupportedException;
use DateTime;

class LogsList extends PagedList
{
    private $logFiles = [];

    public static function loadLogFiles(): array
    {
        $config = ConfigRouter::getDriver()->findConfig('core');
        $logFilePattern = ROOT_DIR . '/' . $config->{'log.dir'} . '/*.txt';
        $result = glob($logFilePattern);
        usort($result, function ($aFile, $bFile) {
            $datetime1 = self::createLogDateTime($aFile);
            $datetime2 = self::createLogDateTime($bFile);
            return $datetime1 == $datetime2 ? 0 :
                -($datetime1 < $datetime2 ? -1 : 1);
        });
        return $result;
    }

    private static function createLogDateTime(string $file): DateTime
    {
        // Переводит формат "d-m-Y.txt" имени файла в формат строки Y-m-d для PHP
        $date = implode('-', array_reverse(explode('-', basename($file, '.txt'))));
        return new DateTime($date);
    }

    public function __construct(int $page)
    {
        $this->logFiles = self::loadLogFiles();
        parent::__construct($page, count($this->logFiles), 1);
    }

    public function countOnPage(): int
    {
        return 1;
    }

    public function getIterator(): \Iterator
    {
        throw new NotSupportedException;
        yield null;
    }

    public function getLogger(): Logger
    {
        $index = $this->getPager()->getCurrent() - 1;
        if (!isset($this->logFiles[$index])) return null;
        return new Logger($this->logFiles[$index]);
    }
}