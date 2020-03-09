<?php namespace frame\tools\units;

abstract class Unit
{
    private $value;
    private $unit;
    private $units = null;

    public abstract static function getOrderedUnits(): array;

    public function __construct(float $value, string $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
        $this->units = static::getOrderedUnits();
    }

    public function convertTo(string $unit): float
    {
        return $this->value * $this->units[$this->unit] / $this->units[$unit];
    }

    public function calcMaxInt(array $allowedUnits = null): array
    {
        $currentValue = $this->value;
        $currentUnit = $this->unit;
        $unitIndex = array_search($currentUnit, $this->units, true);
        if ($unitIndex !== false) {
            for ($i = $unitIndex + 1, $c = count($this->units); $i < $c; ++$i) {
                if ($allowedUnits !== null && array_search(
                    $this->units[$i], $allowedUnits, true
                ) === false) continue;
                $unitValue = $this->convertTo($this->units[$i]);
                if ((int) $unitValue != $unitValue) break;
                $currentValue = $unitValue;
                $currentUnit = $this->units[$i];
            }
        }
        return [$currentValue, $currentUnit];
    }
}