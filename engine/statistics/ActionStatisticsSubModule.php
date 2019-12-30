<?php namespace engine\statistics;

use frame\Core;
use frame\actions\Action;

use engine\statistics\stats\TimeStat;
use engine\statistics\stats\ActionStat;
use engine\statistics\macros\actions\StartCollectActionStat;
use engine\statistics\macros\actions\EndCollectActionStat;
use engine\statistics\macros\actions\CollectActionError;
use engine\statistics\macros\actions\EndCollectAppStat;

class ActionStatisticsSubModule extends BaseStatisticsSubModule
{
    protected function getAppEventHandlers(): array
    {
        $stat = new ActionStat;
        $timer = new TimeStat;

        $collectActionError = new CollectActionError($stat);

        return [
            Action::EVENT_START => new StartCollectActionStat($stat, $timer),
            Action::EVENT_END => new EndCollectActionStat($stat, $timer),
            Core::EVENT_APP_ERROR => $collectActionError,
            Core::EVENT_APP_END => new EndCollectAppStat($stat, $collectActionError)
        ];
    }
}