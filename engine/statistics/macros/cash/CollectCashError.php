<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;

class CollectCashError extends BaseStatCollector
{
    private $callsCollector;

    public function __construct(CollectCashCalls $callsCollector)
    {
        $this->callsCollector = $callsCollector;
    }

    protected function collect(...$args)
    {
        $currentStat = $this->callsCollector->getStatOfNowCreating();
        // Эта ошибка не связана с созданием значения кеша.
        if (!$currentStat) return;
        
        /** @var \Throwable $error */
        $error = $args[0];
        $currentStat->init_error = str_replace('\\', '\\\\', $error->getMessage());
    }
}