<?php namespace engine\admin\logging;

use frame\tools\trackers\read\ReadLimitedProgressTracker;
use frame\tools\Logger;

class LogReadTracker
{
    private $forId;

    public function __construct(int $forId)
    {
        $this->forId = $forId;
    }

    public function countUnreadedRecordsFromAllLogs()
    {
        return $this->createAllLoggingTracker()->loadUnreaded();
    }

    public function countReadedFromLog(string $file): int
    {
        return $this->createLogTracker($file)->loadProgress();
    }

    public function countUnreadedFromLog(string $file): int
    {
        return $this->createLogTracker($file)->loadUnreaded();
    }

    public function setLogReaded(string $file)
    {
        $unreadedCount = $this->countUnreadedFromLog($file);
        $this->createLogTracker($file)->updateSetFinished();
        $allTracker = $this->createAllLoggingTracker();
        $currentProgress = $allTracker->loadProgress();
        $allTracker->updateProgress($currentProgress + $unreadedCount);
    }

    private function countRecordsFromAllLogs(): int
    {
        $logFiles = LogsList::loadLogFiles();
        return array_reduce($logFiles, function($count, $file) {
            $logger = new Logger($file);            
            return $count + count($logger->read());
        }, 0);
    }

    private function createLogTracker(string $file): ReadLimitedProgressTracker
    {
        $logger = new Logger($file);
        return new ReadLimitedProgressTracker(
            'log', crc32($file), count($logger->read()), $this->forId);
    }

    private function createAllLoggingTracker(): ReadLimitedProgressTracker
    {
        $allCount = $this->countRecordsFromAllLogs();
        return new ReadLimitedProgressTracker('log', 0, $allCount, $this->forId);
    }
}