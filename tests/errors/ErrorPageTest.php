<?php
use PHPUnit\Framework\TestCase;
use frame\core\Core;
use frame\errors\Errors;
use frame\route\HttpError;
use tests\errors\stubs\HttpErrorHandlerStub;
use frame\stdlib\cash\config;
use frame\views\ViewRouter;
use tests\errors\stubs\ViewRouterStub;
use frame\config\ConfigRouter;
use frame\stdlib\configs\JsonConfig;

class ErrorPageTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testErrorPageCanBeShownToHandleAnError()
    {
        $app = new Core;
        
        ConfigRouter::getDriver()->addSupport(JsonConfig::class);
        $config = config::get('core');
        $config->{'log.enabled'} = false;
        
        $app->replaceDriver(ViewRouter::class, ViewRouterStub::class);
        
        $errors = Errors::getDriver();
        $errors->setHandler(HttpError::class, HttpErrorHandlerStub::class);

        $this->expectOutputString('Error message example');
        $errors->handleError(new HttpError(500, 'Error message example'));
    }
}