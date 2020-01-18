<?php namespace frame\tools\trackers\read;

use function lightlib\count_file_lines;

class FileReadTracker
{
    private $tracker;

    public function __construct(string $file, ?int $for = null)
    {
        $this->tracker = new ReadLimitedProgressTracker(
            'files',
            crc32($file),
            count_file_lines($file),
            $for
        );
    }

    public function countLines(): int
    {
        return $this->tracker->getLimit();
    }

    public function countOldLines(): int
    {
        return $this->tracker->loadProgress();
    }

    public function countNewLines(): int
    {
        $new = $this->tracker->loadUnreaded();
        if ($new < 0) return 0;
        return $new;
    }

    public function setReaded()
    {
        $this->tracker->updateSetFinished();
    }

    public function setUnreadedForAll()
    {
        $for = $this->tracker->getFor();
        $this->tracker->setFor(null);
        $this->tracker->reset();
        $this->tracker->setFor($for);
    }
}