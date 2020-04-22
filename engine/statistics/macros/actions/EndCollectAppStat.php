<?php namespace engine\statistics\macros\actions;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\ActionStat;
use frame\route\Response;

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

        $this->stat->insert();
    }
}