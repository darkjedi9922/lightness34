<?php namespace engine\statistics\macros\actions;

use frame\actions\Action;
use engine\statistics\stats\ActionStat;
use engine\statistics\stats\TimeStat;
use engine\statistics\macros\BaseStatCollector;

class EndCollectActionStat extends BaseStatCollector
{
    private $stat;
    private $timer;
    private $action = null;
    private $hasErrors = false;

    public function __construct(ActionStat $stat, TimeStat $timer)
    {
        $this->stat = $stat;
        $this->timer = $timer;
    }

    public function hasActionErrors(): bool
    {
        return $this->hasErrors;
    }

    protected function collect(...$args)
    {
        /** @var Action $action */
        $this->action = $args[0];
        $this->hasErrors = $this->action->hasErrors();

        $this->stat->duration_sec = $this->timer->resultInSeconds();
        $this->updateResultData($this->action);
    }

    private function updateResultData(Action $action)
    {
        $data = str_replace('\\\\', '\\', $this->stat->data_json);
        $data = json_decode($data, true);

        $data['errors'] = $action->getErrors();
        $data['result'] = $action->getResult();
        
        $jsonData = json_encode($data, JSON_HEX_AMP);
        $this->stat->data_json = str_replace('\\', '\\\\', $jsonData);
    }
}