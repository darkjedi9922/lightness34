<?php namespace frame\tools;

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
        if ($currentValue != 0) {
            $units = array_keys($this->units);
            $unitIndex = array_search($currentUnit, $units, true);
            if ($unitIndex !== false) {
                for ($i = $unitIndex + 1, $c = count($units); $i < $c; ++$i) {
                    if (!$this->isUnitAllowed($units[$i], $allowedUnits)) continue;
                    $unitValue = $this->convertTo($units[$i]);
                    if ((int) $unitValue != $unitValue) break;
                    $currentValue = $unitValue;
                    $currentUnit = $units[$i];
                }
            }
        }
        return [$currentValue, $currentUnit];
    }

    public function calcConvenientForm(
        int $precision = 1,
        array $allowedUnits = null
    ): array {
        $currentValue = $this->value;
        $currentUnit = $this->unit;
        if ($currentValue != 0) {
            $units = array_keys($this->units);
            $unitIndex = array_search($currentUnit, $units, true);
            if ($unitIndex !== false) {
                $step = $currentValue >= 1 ? 1 : -1;
                for ($i = $unitIndex + $step, $c = count($units); $i < $c; $i += $step) {
                    if (!$this->isUnitAllowed($units[$i], $allowedUnits)) continue;
                    $unitValue = $this->convertTo($units[$i]);
                    if ($step === 1 && $unitValue < 1) break;
                    else if ($step === -1 && $unitValue >= 1) {
                        $currentValue = $unitValue;
                        $currentUnit = $units[$i];
                        break;
                    }
                    $currentValue = $unitValue;
                    $currentUnit = $units[$i];
                }
            }
        }
        return [round($currentValue, $precision), $currentUnit];
    }

    private function isUnitAllowed(string $unit, ?array $allowedUnits): bool
    {
        if ($allowedUnits === null) return true;
        return in_array($unit, $allowedUnits, true);
    }
}