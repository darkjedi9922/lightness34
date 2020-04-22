<?php namespace frame\views;

class DynamicPage extends Page
{
    public static function getExtensions(): array
    {
        return ['php'];
    }

    public function getArguments(): array
    {
        return $this->getMeta('$') ?? [];
    }

    public function getArgument(int $index): ?string
    {
        return $this->getMeta('$')[$index] ?? null;
    }
}