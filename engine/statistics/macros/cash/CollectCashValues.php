<?php namespace engine\statistics\macros\cash;

use engine\statistics\macros\BaseStatCollector;
use engine\statistics\stats\CashValueStat;

class CollectCashValues extends BaseStatCollector
{
    private $valueStats = [];

    public function getValueStats(): array
    {
        return $this->valueStats;
    }

    protected function collect(...$args)
    {
        $class = $args[0];
        $key = $args[1];
        
        if (isset($this->valueStats[$class][$key])) {
            /** @var CashValueStat $valueStat */
            $valueStat = $this->valueStats[$class][$key];
            $valueStat->call_count += 1;
        } else {
            $valueStat = new CashValueStat;
            $valueStat->class = str_replace('\\', '\\\\', $class);
            $valueStat->value_key = $key;
            $valueStat->call_count = 1;
            $this->valueStats[$class][$key] = $valueStat;
        }
    }
}