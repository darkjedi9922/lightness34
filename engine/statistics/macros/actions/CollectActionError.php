<?php namespace engine\statistics\macros\actions;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\ActionStat;

class CollectActionError extends BaseStatCollector
{
    private $stat;
    private $executed = false;

    public function __construct(ActionStat $stat)
    {
        $this->stat = $stat;
    }

    public function isExecuted(): bool
    {
        return $this->executed;
    }

    protected function collect(...$args)
    {
        // Действие не запускалось в этом процессе приложения
        if (!$this->stat->class) return;

        /** @var \Throwable $error */
        $error = $args[0];
        $this->stat->response_type = ActionStat::RESPONSE_TYPE_ERROR;
        $this->stat->response_info = str_replace('\\', '\\\\', $error->getMessage());
        $this->executed = true;
    }
}