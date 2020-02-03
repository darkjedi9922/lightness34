<?php namespace engine\statistics;

use frame\core\Core;
use frame\actions\Action;

use engine\statistics\stats\TimeStat;
use engine\statistics\stats\ActionStat;
use engine\statistics\macros\actions\StartCollectActionStat;
use engine\statistics\macros\actions\EndCollectActionStat;
use engine\statistics\macros\actions\CollectActionError;
use engine\statistics\macros\actions\EndCollectAppStat;
use frame\database\Records;
use frame\modules\Module;

class ActionStatisticsSubModule extends BaseStatisticsSubModule
{
    private $stat;
    private $timer;
    private $collectActionError;

    public function __construct(string $name, ?Module $parent = null)
    {
        parent::__construct($name, $parent);

        $this->stat = new ActionStat;
        $this->timer = new TimeStat;
        $this->collectActionError = new CollectActionError(
            $this->stat,
            $this->timer
        );
    }

    public function clearStats()
    {
        Records::from(ActionStat::getTable())->delete();
    }

    public function endCollecting()
    {
        (new EndCollectAppStat(
            $this->stat,
            $this->collectActionError
        ))->exec();
    }

    public function getAppEventHandlers(): array
    {
        return [
            Action::EVENT_START => new StartCollectActionStat(
                $this->stat,
                $this->timer
            ),
            Action::EVENT_END => new EndCollectActionStat(
                $this->stat,
                $this->timer
            ),
            Core::EVENT_APP_ERROR => $this->collectActionError
        ];
    }
}