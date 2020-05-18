<?php namespace engine\statistics\macros\actions;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\ActionStat;
use frame\route\Response;
use engine\statistics\stats\RouteStat;
use frame\database\Records;
use engine\statistics\macros\actions\EndCollectActionStat;

class EndCollectAppStat extends BaseStatCollector
{
    private $stat;
    private $routeStat;
    private $errorCollector;
    private $endActionCollector;

    public function __construct(
        ActionStat $stat,
        RouteStat $routeStat,
        CollectActionError $errorCollector,
        EndCollectActionStat $endActionCollector
    ) {
        $this->stat = $stat;
        $this->routeStat = $routeStat;
        $this->errorCollector = $errorCollector;
        $this->endActionCollector = $endActionCollector;
    }

    protected function collect(...$args)
    {
        // Действие не запускалось в этом процессе приложения
        if (!$this->stat->class) return;

        // Если запускался errorCollector, он уже сам добавил response данные про
        // ошибку. В данном случае добавляем только если ошибок не было.
        if (!$this->errorCollector->isExecuted()) {
            // Экшн может завершится редиректом. Был ли установлен url на редирект,
            // можно узнать в Response.
            if (Response::getDriver()->getUrl() !== null) {
                $this->stat->response_type = ActionStat::RESPONSE_TYPE_REDIRECT;
                $this->stat->response_info = Response::getDriver()->getUrl();
            } else {
                // Если экшн завершается не редиректом, он выводит результат в json.
                $this->stat->response_type = ActionStat::RESPONSE_TYPE_JSON;
                $this->stat->response_info = null;
            }
        }

        $this->stat->route_id = $this->routeStat->getId();
        $actionId = $this->stat->insert();

        $isFatal = $this->stat->response_type == ActionStat::RESPONSE_TYPE_ERROR;
        $isFail = $isFatal && $this->endActionCollector->hasActionErrors();
        Records::from('stat_action_counts')->insert([
            'action_id' => $actionId,
            'status' => $isFail ? 1 : ($isFatal ? 2 : 0)
        ]);
    }
}