<?php namespace frame\views;

class DynamicPage extends Page
{
    private $args;

    public static function getExtensions(): array
    {
        return ['php'];
    }

    /** {@inheritdoc} */
    public static function find(string $name): ?string
    {
        $parts = explode('/', $name);
        $lastIndex = count($parts) - 1;
        $parts[$lastIndex] = '$'.$parts[$lastIndex];
        return parent::find(implode('/', $parts));
    }

    public function __construct(string $name, array $args, ?string $layout = null)
    {
        $this->args = $args;
        parent::__construct($name, $layout);
    }

    public function getArguments(): array
    {
        return $this->args;
    }

    public function getArgument(int $index): ?string
    {
        return $this->args[$index] ?? null;
    }

    public function hasArgument(int $index): bool
    {
        return isset($this->args[$index]);
    }
}