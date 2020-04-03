<?php namespace frame\views\macros;

use frame\stdlib\cash\router;
use frame\errors\HttpError;
use frame\views\DynamicPage;
use frame\views\Page;

class ShowPage extends \frame\events\Macro
{
    public function exec(...$args)
    {
        $pagename = router::get()->pagename;
        $page = $this->findPage($pagename);
        if ($page) $page->show();
        else throw new HttpError(404, 'Page ' . $pagename . ' does not exist.');
    }

    private function findPage(string $pagename): ?Page
    {
        if (Page::find($pagename)) return new Page($pagename);

        $parts = explode('/', $pagename);

        // Если в url вообще не будет задано частей страницы, то она точно не
        // динамическая т.к. для нее должно быть хотя бы одна часть url,
        // после имени динамической страницы.
        if ($pagename !== '' && DynamicPage::find(''))
            return new DynamicPage('', $parts);

        $page = '';
        $pathCount = count($parts);
        for ($i = 0; $i < $pathCount - 1; ++$i) {
            $newPath = $page . $parts[$i];
            if (DynamicPage::find($newPath))
                return new DynamicPage($newPath, array_slice($parts, $i + 1));
            $page .= $parts[$i] . '/';
        }

        return null;
    }
}