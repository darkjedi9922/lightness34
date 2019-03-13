<?php namespace tests\engine;

class CallbackProvider
{
    public function sumCallback($a, $b)
    {
        return $a + $b;
    }

    public function __invoke($a, $b)
    {
        return $a + $b;
    }
}