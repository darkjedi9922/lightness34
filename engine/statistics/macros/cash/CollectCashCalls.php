<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashValueStat;
use engine\statistics\stats\TimeStat;

class CollectCashCalls extends BaseStatCollector
{
    private $valueStats = [];
    private $creating = null;

    public function getValueStats(): array
    {
        return $this->valueStats;
    }

    public function getStatOfNowCreating(): ?CashValueStat
    {
        return $this->creating;
    }

    protected function collect(...$args)
    {
        $class = $args[0];
        $key = $args[1];
        $creator = $args[2];
        
        if (isset($this->valueStats[$class][$key])) {
            /** @var CashValueStat $valueStat */
            $valueStat = $this->valueStats[$class][$key];
            $valueStat->call_count += 1;
        } else {
            $valueStat = new CashValueStat;
            $valueStat->class = str_replace('\\', '\\\\', $class);
            $valueStat->value_key = $key;
            $valueStat->call_count = 1;

            // Вставляем до попытки создать. Если при создании кеша будут ошибки,
            // stat не будет вставлен в массив потом, следовательно, ошибка не
            // попадет в БД.
            $this->valueStats[$class][$key] = $valueStat;

            $timer = new TimeStat;
            $this->creating = $valueStat;
            $timer->start();
            $creator();
            $valueStat->init_duration_sec = $timer->resultInSeconds();
            $this->creating = null;
        }
    }
}