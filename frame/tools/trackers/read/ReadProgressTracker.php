<?php namespace frame\tools\trackers\read;

class ReadProgressTracker extends ReadTracker
{
    public function loadProgress(): int
    {
        return $this->getRecords()->select(['progress'])->readScalar() ?? 0;
    }

    public function updateProgress(int $progress)
    {
        if ($progress <= 0) $this->reset();
        else {
            $data = ['progress' => $progress];
            $records = $this->getRecords();
            if ($records->select(['progress'])->count()) $records->update($data);
            else $records->insert($data);
        }
    }
}