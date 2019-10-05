<?php namespace frame\views;

use function lightlib\last;

class DynamicPage extends Page
{
    private $args;

    /** {@inheritdoc} */
    public static function find(string $name): ?string
    {
        $parts = explode('/', $name);
        $file = self::FOLDER . '/$' . last($parts) . '.php';
        return file_exists($file) ? $file : null;
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

    public function getArgument(int $index): string
    {
        return $this->args[$index];
    }

    public function hasArgument(int $index): bool
    {
        return isset($this->args[$index]);
    }
}