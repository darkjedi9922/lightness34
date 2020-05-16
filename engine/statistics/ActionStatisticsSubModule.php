<?php namespace engine\statistics;

use engine\statistics\stats\TimeStat;
use engine\statistics\stats\ActionStat;
use engine\statistics\macros\actions\StartCollectActionStat;
use engine\statistics\macros\actions\EndCollectActionStat;
use engine\statistics\macros\actions\CollectActionError;
use engine\statistics\macros\actions\EndCollectAppStat;
use frame\database\Records;
use frame\actions\Action;
use frame\modules\Module;
use frame\errors\Errors;
use engine\statistics\stats\RouteStat;

class ActionStatisticsSubModule extends BaseStatisticsSubModule
{
    private $stat;
    private $timer;
    private $routeStat;
    private $collectActionError;
    private $endActionCollector;

    public function __construct(
        string $name,
        RouteStat $routeStat,
        ?Module $parent = null
    ) {
        parent::__construct($name, $parent);

        $this->stat = new ActionStat;
        $this->timer = new TimeStat;
        $this->routeStat = $routeStat;
        $this->collectActionError = new CollectActionError(
            $this->stat, $this->timer);
        $this->endActionCollector = new EndCollectActionStat(
            $this->stat, $this->timer);
    }

    public function clearStats()
    {
        Records::from(ActionStat::getTable())->delete();
        Records::from('stat_action_counts')->delete();
    }

    public function endCollecting()
    {
        (new EndCollectAppStat(
            $this->stat,
            $this->routeStat,
            $this->collectActionError,
            $this->endActionCollector
        ))->exec();
    }

    public function getAppEventHandlers(): array
    {
        return [
            Action::EVENT_START => new StartCollectActionStat(
                $this->stat,
                $this->timer
            ),
            Action::EVENT_END => $this->endActionCollector,
            Errors::EVENT_ERROR => $this->collectActionError
        ];
    }
}