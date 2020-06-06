<?php

use frame\api\ExecApi;
use frame\config\ConfigRouter;
use frame\core\Core;
use frame\events\Events;
use frame\route\Request;
use frame\route\Response;
use frame\stdlib\configs\JsonConfig;
use frame\tools\JsonEncoder;
use PHPUnit\Framework\TestCase;
use tests\api\examples\SimpleApi;
use tests\route\stubs\RequestStub;
use tests\route\stubs\ResponseStub;
use frame\route\Router;
use frame\http\route\UrlRouter;

class ExecApiTest extends TestCase
{
    public function testExecutesApiAndReturnsJsonAsResult()
    {
        $app = new Core([Router::class => UrlRouter::class]);
        $app->replaceDriver(Request::class, RequestStub::class);
        $app->replaceDriver(Response::class, ResponseStub::class);
        ConfigRouter::getDriver()->addSupport(JsonConfig::class);
        RequestStub::getDriver()->setRequest('/tests/api/examples/simple');
        Events::getDriver()->on(Core::EVENT_APP_START, new ExecApi('tests/api'));

        $app->exec();

        $prettyJson = ConfigRouter::getDriver()->findConfig('core')->{'mode.debug'};
        $expectedResult = SimpleApi::$expectedResult;
        $expectedOutput = JsonEncoder::forViewText($expectedResult, $prettyJson);

        $this->assertEquals($expectedOutput, ResponseStub::getDriver()->text);
    }
}