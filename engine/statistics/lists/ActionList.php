<?php namespace engine\statistics\lists;

use frame\lists\base\BaseList;
use frame\actions\ActionBody;
use function lightlib\remove_prefix;
use function lightlib\remove_suffix;

class ActionList implements BaseList
{
    private $list = [];

    public function __construct()
    {
        $this->list = $this->search('\\engine');
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->list);
    }

    public function count(): int
    {
        return count($this->list);   
    }

    private function search(string $namespace): array
    {
        $result = [];
        $it = new \FilesystemIterator($this->getNamespaceDir($namespace));
        foreach ($it as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDir()) {
                $found = $this->search("$namespace\\{$file->getBasename()}");
                $result = array_merge($result, $found);
            } else if ($file->isFile()) {
                $class = $this->getActionClass($file);
                if ($class !== null) $result[] = $class;
            };
        }
        return $result;
    }

    private function getNamespaceDir(string $namespace): string
    {
        return ROOT_DIR . '/' . trim(str_replace('\\', '/', $namespace), '/');
    }

    private function getActionClass(\SplFileInfo $file): ?string
    {
        $name = remove_prefix(remove_suffix((string) $file, ".php"), ROOT_DIR);
        $class = str_replace('/', '\\', $name);
        if (!class_exists($class)) return null;
        if (!is_subclass_of($class, ActionBody::class)) return null;
        return $class;
    }
}