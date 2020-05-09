<?php

use PHPUnit\Framework\TestCase;
use frame\actions\Action;
use frame\route\HttpError;
use frame\actions\ActionRouter;
use frame\actions\ActionToken;
use tests\actions\examples\EmptyActionExample;

/**
 * @runTestsInSeparateProcesses
 */
class ActionTokenTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSetsTokenToTheGet()
    {
        $action = new Action(new EmptyActionExample);
        $this->assertNull($action->getData('get', ActionToken::GET_KEY));

        $tokenizer = new ActionToken($action);
        $tokenizer->tokenize();

        $this->assertNotNull($action->getData('get', ActionToken::GET_KEY));
    }

    public function testTokenizesAnActionThatPassesToItsTriggerUrl()
    {
        $srcAction = new Action(new EmptyActionExample);
        $srcTokenizer = new ActionToken($srcAction);
        $srcToken = $srcTokenizer->getExpectedToken();

        $srcTokenizer->tokenize();

        $router = new ActionRouter;
        $url = $router->getTriggerUrl($srcAction);
        $destAction = $router->fromTriggerUrl($url);

        $destTokenizer = new ActionToken($destAction);
        $destToken = $destTokenizer->getActualToken();

        $this->assertEquals($srcToken, $destToken);
    }

    public function testCorrectTokenPassesValidation()
    {
        $action = new Action(new EmptyActionExample);
        $tokenizer = new ActionToken($action);
        $tokenizer->tokenize();
        $tokenizer->validate(); // If does not pass, HttpError exception is raised
        $this->assertTrue(true);
    }

    public function testIncorrectTokenThrowsHttpError()
    {
        $action = new Action(new EmptyActionExample, [
            ActionToken::GET_KEY => 'incorrect-token'
        ]);
        $tokenizer = new ActionToken($action);

        $this->expectException(HttpError::class);
        
        $tokenizer->validate();
    }

    public function testNoTokenIsIncorrectToken()
    {
        // Никакой токен не передается.
        $action = new Action(new EmptyActionExample);
        $tokenizer = new ActionToken($action);

        $this->expectException(HttpError::class);

        $tokenizer->validate();
    }
}