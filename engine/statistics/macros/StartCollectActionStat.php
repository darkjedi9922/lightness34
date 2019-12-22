<?php namespace engine\statistics\macros;

use frame\actions\Action;
use engine\statistics\stats\ActionStat;
use frame\route\Request;
use engine\statistics\stats\TimeStat;

class StartCollectActionStat extends BaseStatCollector
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
        
        $class = get_class($action->getBody());
        $this->stat->class = '\\\\' . str_replace('\\', '\\\\', $class);
        $this->stat->ajax = Request::isAjax();
        $this->stat->time = time();
        
        $this->timer->start();
    }
}