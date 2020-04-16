<?php

use frame\api\ExecApi;
use frame\config\ConfigRouter;
use frame\core\Core;
use frame\events\Events;
use frame\route\Request;
use frame\route\Response;
use frame\stdlib\cash\config;
use frame\stdlib\configs\JsonConfig;
use frame\tools\JsonEncoder;
use PHPUnit\Framework\TestCase;
use tests\api\examples\SimpleApi;
use tests\route\stubs\RequestStub;
use tests\route\stubs\ResponseStub;

class ExecApiTest extends TestCase
{
    public function testExecutesApiAndReturnsJsonAsResult()
    {
        $app = new Core;
        $app->replaceDriver(Request::class, RequestStub::class);
        $app->replaceDriver(Response::class, ResponseStub::class);
        ConfigRouter::getDriver()->addSupport(JsonConfig::class);
        RequestStub::getDriver()->setRequest('/tests/api/examples/simple');
        Events::getDriver()->on(Core::EVENT_APP_START, new ExecApi('tests/api'));

        $app->exec();

        $prettyJson = config::get('core')->{'mode.debug'};
        $expectedResult = SimpleApi::$expectedResult;
        $expectedOutput = JsonEncoder::forViewText($expectedResult, $prettyJson);

        $this->assertEquals($expectedOutput, ResponseStub::getDriver()->text);
    }
}