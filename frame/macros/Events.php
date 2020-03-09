<?php namespace frame\macros;

use frame\core\Engine;

class Events extends Engine
{
    public function emit(string $event, ...$args): array
    {
        $result = [];
        $subscribers = $this->subscribers[$event] ?? [];
        if (!empty($subscribers)) {
            for ($i = 0, $c = count($subscribers); $i < $c; ++$i) {
                $macro = $this->subscribers[$event][$i];
                $result[] = $macro;
                $macro(...$args);
            }
        }
        return $result;
    }
}