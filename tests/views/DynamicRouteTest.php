<?php
use PHPUnit\Framework\TestCase;
use tests\views\stubs\ViewRouterStub;
use frame\route\Router;
use frame\core\Core;
use frame\views\ViewRouter;

class DynamicRouteTest extends TestCase
{
    /**
     * @dataProvider pagePathsProvider
     */
    public function testFindsDynamicPage(string $url, ?string $resultViewfile)
    {
        $app = new Core;
        $app->replaceDriver(ViewRouter::class, ViewRouterStub::class);

        $urlRouter = new Router($url);
        $viewRouter = new ViewRouterStub;
        $page = $viewRouter->findPage($urlRouter);
        if ($resultViewfile === null) $this->assertNull($page);
        else {
            $this->assertNotNull($page);
            $resultViewfile = $viewRouter->getBaseFolder() . '/' . $resultViewfile;
            $this->assertEquals($resultViewfile, $page->file);
        }
    }

    public function pagePathsProvider(): array
    {
        return [
            ['/profile', 'pages/profile.html'],
            ['/profile/jed/groups', 'pages/profile/$meta/groups.php'],
            ['/profile/jed/friends', 'pages/profile/$meta/$meta.php'],
            ['/profile/jed/friends/add', 'pages/profile/$meta/$meta/$meta.php'],
            ['/profile/jed/groups/add', 'pages/profile/$meta/$meta/$meta.php']
        ];
    }
}