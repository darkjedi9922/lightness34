<?php namespace frame\views;

use frame\route\Route;
use frame\views\Page;

class ViewRouter extends \frame\core\Driver
{
    private $pagesDir;

    public function getBaseFolder(): string
    {
        return ROOT_DIR . '/views';
    }

    public function findPage(Route $router): ?Page
    {
        $pagename = $router->pagename;
        if (Page::find($pagename)) return new Page($pagename);
        $this->pagesDir = $this->getBaseFolder() . '/' . DynamicPage::getNamespace();
        return $this->findPageRecusive('', $router->getPathParts(), 0, []);
    }

    private function findPageRecusive(
        string $path,
        array $pathParts,
        int $partIndex,
        array $dynamicArgs
    ): ?Page {
        $page = null;

        if ($partIndex < count($pathParts) - 1) {
            $fullPath = $this->pagesDir . ($path === '' ? '' : "/$path");
            $delimiter = ($path === '' ? '' : '/');
            // Сначала пытаемся рекурсивно найти страницу в папке с точным именем.
            if (is_dir("$fullPath/" . $pathParts[$partIndex]))
                $page = $this->findPageRecusive(
                    $path . $delimiter . $pathParts[$partIndex],
                    $pathParts,
                    $partIndex + 1,
                    $dynamicArgs
                );
            
            // Если там не нашли, ищем рекурсивно в директории с динамическим именем.
            // Если и тут не найдем, $page останется null, его и вернем.
            if (!$page && is_dir("$fullPath/\$meta")) {
                $dynamicArgs[] = $pathParts[$partIndex];
                $page = $this->findPageRecusive(
                    $path . $delimiter . '$meta',
                    $pathParts,
                    $partIndex + 1,
                    $dynamicArgs
                );
            }
        } else {
            // Если это последний компонент пути - ищем именно конечную страницу.
            // Она либо имеет точное название, ...
            $page = $this->findEndPage("$path/" . $pathParts[$partIndex]);
            if (!$page) {
                // ... либо динамическое.
                $page = $this->findEndPage("$path/\$meta");
                $dynamicArgs[] = $pathParts[$partIndex];
            }
        }

        if ($page && !empty($dynamicArgs)) $page->setMeta('$', $dynamicArgs);
        return $page;
    }

    private function findEndPage(string $name): ?Page
    {
        if (DynamicPage::find($name) === null) return null;
        $page = new DynamicPage($name);
        return $page;
    }
}