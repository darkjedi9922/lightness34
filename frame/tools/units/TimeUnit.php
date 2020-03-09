<?php namespace frame\tools\units;

class TimeUnit
{
    const SECONDS = 1;
    const MINUTES = self::SECONDS * 60;
    const HOURS = self::MINUTES * 60;
    const DAYS = self::HOURS * 24;
    const MONTHS = self::DAYS * 30;
    const YEARS = self::MONTHS * 12;

    private const UNITS = [
        self::SECONDS,
        self::MINUTES,
        self::HOURS,
        self::DAYS,
        self::MONTHS,
        self::YEARS
    ];

    private $value;
    private $unit;

    public function __construct(float $value, int $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    public function convertTo(int $unit): float
    {
        return $this->value * $this->unit / $unit;
    }

    public function calcMaxInt(array $allowedUnits = null): array
    {
        $currentValue = $this->value;
        $currentUnit = $this->unit;
        $unitIndex = array_search($currentUnit, self::UNITS, true);
        if ($unitIndex !== false) {
            for ($i = $unitIndex + 1, $c = count(self::UNITS); $i < $c; ++$i) {
                if (   $allowedUnits !== null 
                    && array_search(self::UNITS[$i], $allowedUnits, true) === false
                ) continue;
                $unitValue = $this->convertTo(self::UNITS[$i]);
                if ((int) $unitValue != $unitValue) break;
                $currentValue = $unitValue;
                $currentUnit = self::UNITS[$i];
            }
        }
        return [$currentValue, $currentUnit];
    }
}