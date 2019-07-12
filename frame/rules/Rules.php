<?php namespace frame\rules;

class Rules
{
    const RULE_DIR = ROOT_DIR . '/rules';

    public static function loadRule(string $rule): ?callable
    {
        $file = self::RULE_DIR . '/' . $rule . '.php';
        if (file_exists($file)) return require($file);
        return null;
    }
}