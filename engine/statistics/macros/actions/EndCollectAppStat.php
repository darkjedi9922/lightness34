<?php namespace engine\statistics\macros\actions;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\ActionStat;
use frame\route\Response;

use frame\cash\config;
use frame\cash\database;

class EndCollectAppStat extends BaseStatCollector
{
    private $stat;
    private $errorCollector;

    public function __construct(ActionStat $stat, CollectActionError $errorCollector)
    {
        $this->stat = $stat;
        $this->errorCollector = $errorCollector;
    }

    protected function collect(...$args)
    {
        // Действие не запускалось в этом процессе приложения
        if (!$this->stat->class) return;

        if (!$this->errorCollector->isExecuted()) {
            if (Response::getUrl() !== null) {
                $this->stat->response_type = ActionStat::RESPONSE_TYPE_REDIRECT;
                $this->stat->response_info = Response::getUrl();
            } else {
                $this->stat->response_type = ActionStat::RESPONSE_TYPE_JSON;
                $this->stat->response_info = null;
            }
        }

        $this->stat->insert();
        $this->deleteOldStats();
    }

    private function deleteOldStats()
    {
        $actionTable = ActionStat::getTable();
        $time = time() - config::get('statistics')->storeTimeInSeconds;
        database::get()->query(
            "DELETE $actionTable FROM $actionTable
            WHERE $actionTable.time < $time"
        );
    }
}