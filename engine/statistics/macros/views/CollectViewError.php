<?php namespace engine\statistics\macros\views;

use engine\statistics\macros\BaseStatCollector;
use Throwable;

class CollectViewError extends BaseStatCollector
{
    private $startCollector;

    public function __construct(StartCollectViewStats $startCollector)
    {
        $this->startCollector = $startCollector;
    }

    protected function collect(...$args)
    {
        $currentViewStat = $this->startCollector->getCurrentViewStat();
        // Ошибка возникла не во время загрузки вида.
        if ($currentViewStat === null) return;

        /** @var Throwable $error */
        $error = $args[0];
        $currentViewStat->error = str_replace('\\', '\\\\', $error->getMessage());
    }
}