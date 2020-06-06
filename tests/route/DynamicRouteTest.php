<?php
use PHPUnit\Framework\TestCase;
use frame\route\Route;
use tests\route\examples\DynamicRouterMock;
use frame\core\Core;
use frame\route\Router;
use frame\http\route\UrlRouter;

class DynamicRouteTest extends TestCase
{
    private $routes = [
        'profile',
        'profile/?/?',
        'profile/?/?/?',
        'profile/?/groups',
        'profile/?/groups/remove'
    ];

    public static function setUpBeforeClass(): void
    {
        $app = new Core([
            Router::class => UrlRouter::class
        ]);
    }

    /**
     * @dataProvider routeProvider
     */
    public function testFindsDynamicRoute(
        string $requiredRoute,
        string $expectedRealRoute,
        array $expectedRealArgs
    ) {
        $dynamicTag = '?';
        $dynamicRouter = new DynamicRouterMock($dynamicTag, $this->routes);
        $expectedRealRoute = new Route(
            $expectedRealRoute,
            $expectedRealRoute,
            explode('/', $expectedRealRoute),
            $expectedRealArgs
        );
        
        $actualRealRoute = $dynamicRouter->findRealRoute($requiredRoute);
        $this->assertNotNull($actualRealRoute);
        $this->assertEquals($expectedRealRoute->url, $actualRealRoute->url);
        $this->assertEquals($expectedRealArgs, $actualRealRoute->args);
    }

    public function routeProvider(): array
    {
        return [
            ['profile', 'profile', []],
            ['profile/jed/groups', 'profile/?/groups', ['jed']],
            ['profile/jed/friends', 'profile/?/?', ['jed', 'friends']],
            ['profile/jed/friends/add', 'profile/?/?/?', ['jed', 'friends', 'add']],
            ['profile/jed/groups/add', 'profile/?/?/?', ['jed', 'groups', 'add']]
        ];
    }
}