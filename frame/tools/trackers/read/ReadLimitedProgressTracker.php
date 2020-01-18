<?php namespace frame\tools\trackers\read;

class ReadLimitedProgressTracker extends ReadProgressTracker
{
    private $limit;

    public function __construct(
        string $name,
        int $what,
        int $limit,
        ?int $for = null
    ) {
        parent::__construct($name, $what, $for);
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function loadIsFinished(): bool
    {
        return $this->loadProgress() === $this->limit;
    }

    public function updateSetFinished()
    {
        $this->updateProgress($this->limit);
    }

    public function loadUnreaded(): int
    {
        return $this->limit - $this->loadProgress();
    }
}