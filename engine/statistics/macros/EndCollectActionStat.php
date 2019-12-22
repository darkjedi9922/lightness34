<?php namespace engine\statistics\macros;

use frame\actions\Action;
use engine\statistics\stats\ActionStat;
use engine\statistics\stats\TimeStat;

class EndCollectActionStat extends BaseStatCollector
{
    private $stat;
    private $timer;

    public function __construct(ActionStat $stat, TimeStat $timer)
    {
        $this->stat = $stat;
        $this->timer = $timer;
    }

    protected function collect(...$args)
    {
        /** @var Action $action */
        $action = $args[0];

        $this->stat->duration_sec = $this->timer->resultInSeconds();
        $this->stat->insert();
    }
}