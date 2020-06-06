<?php

use PHPUnit\Framework\TestCase;
use tests\engine\UserDeleteAction;
use tests\actions\examples\GetListActionExample;
use tests\actions\examples\PostListActionExample;
use tests\actions\examples\BoolPostListActionExample;
use tests\actions\examples\AlwaysSucceedActionExample;
use tests\actions\examples\AlwaysFailActionExample;
use frame\route\HttpError;
use frame\actions\ActionRouter;
use frame\actions\Action;
use frame\actions\fields\FileField;
use frame\actions\UploadedFile;
use frame\core\Core;
use tests\actions\examples\EmptyActionExample;
use tests\actions\examples\FileFieldActionExample;
use frame\route\Router;
use frame\http\route\UrlRouter;

class ActionTest extends TestCase
{
    private $fileExampleData = [
        'name' => 'some-file.txt',
        'type' => 'image/jpeg',
        'size' => 3600,
        'tmp_name' => 'some-tmp-name.txt',
        'error' => UploadedFile::UPLOAD_ERR_OK
    ];

    public static function setUpBeforeClass(): void
    {
        $app = new Core([Router::class => UrlRouter::class]);
    }

    public function testDefaultIdIsEmptyString()
    {
        $action = new Action(new UserDeleteAction);
        $this->assertEquals('', $action->getId());
    }

    public function testIdIsEmptyStringIfItWasNotRecievedFromUrl()
    {
        $router = new ActionRouter;
        $action = $router->fromTriggerUrl('/tests/engine/UserDeleteAction');
        $this->assertEquals('', $action->getId());
    }

    public function testThrowsNotFoundIfThereIsNotRecievedListedGetData()
    {
        $action = new Action(new GetListActionExample);

        $this->expectException(HttpError::class);
        $this->expectExceptionCode(HttpError::NOT_FOUND);
        $action->exec();
    }

    public function testDoesNotThrowNotFoundIfThereIsRecievedListedGetData()
    {
        $action = new Action(new GetListActionExample, [
            'name' => 'SomeName',
            'amount' => '12'
        ]);
        $action->exec();
        $this->assertFalse($action->hasErrors());
    }

    public function testConvertsGetArgsToSpecifiedType()
    {
        $action = new Action(new GetListActionExample, ['amount' => '42']);
        $amount = $action->getData('get', 'amount');

        $this->assertIsInt($amount);
    }

    public function testThrowsNotFoundIfThereIsNotRecievedListedPostData()
    {
        $action = new Action(new PostListActionExample);
        $this->expectException(HttpError::class);
        $this->expectExceptionCode(HttpError::NOT_FOUND);
        $action->exec();
    }

    public function testDoesNotThrowNotFoundIfThereIsRecievedListedPostData()
    {
        $action = new Action(new PostListActionExample);
        $action->setData('post', 'sum', '66');
        $action->exec();
        $this->assertFalse($action->hasErrors());
    }

    public function testConvertsSpecifiedIntegerPostArgsToInteger()
    {
        $action = new Action(new PostListActionExample);
        $action->setData('post', 'sum', '66');
        
        $value = $action->getData('post', 'sum');
        $this->assertIsInt($value);
    }

    /**
     * @dataProvider boolPostProvider
     */
    public function testConvertsSpecifiedPostArgsToBool($value, bool $boolValue)
    {
        $action = new Action(new BoolPostListActionExample);
        $action->setData('post', 'checked', $value);

        $convertedValue = $action->getData('post', 'checked');
        $this->assertEquals($boolValue, $convertedValue);
    }

    public function testIfABoolValueWasNotRecievedThenItEqualsNullBeforeExec()
    {
        $action = new Action(new BoolPostListActionExample);
        $this->assertNull($action->getData('post', 'checked'));
    }

    public function testIfABoolValueWasNotRecievedThenItEqualsFalseDuringExec()
    {
        $body = new BoolPostListActionExample;
        (new Action($body))->exec();
        $this->assertIsBool($body->checked);
    }

    public function boolPostProvider()
    {
        return [
            [true, true],
            [false, false],
            [null, false],
            ['0', false],
            ['1', true],
            ['', false],
            ['some-text', true],
            ['true', true],
            ['false', false]
        ];
    }

    public function testConvertsSpecifiedFilesToAFileFieldClass()
    {
        $file = new UploadedFile($this->fileExampleData);
        $body = new FileFieldActionExample;
        $action = new Action($body);
        $action->setData(Action::FILES, 'avatar', $file);

        $action->exec();

        $this->assertInstanceOf(FileField::class, $body->avatarField);
        $this->assertEquals($file, $body->avatarField->get());
    }

    public function testNoExecutedActionHasEmptyResult()
    {
        $action = new Action(new AlwaysSucceedActionExample);
        $this->assertEmpty($action->getResult());
    }

    public function testResultOfTheSuccessIsSavedInTheActionResult()
    {
        $action = new Action(new AlwaysSucceedActionExample);
        $action->exec();
        $this->assertEquals(['resultAnswer' => 42], $action->getResult());
    }

    public function testResultOfFailIsSavedInTheActionResult()
    {
        $action = new Action(new AlwaysFailActionExample);
        $action->exec();
        $this->assertEquals(['doctor' => 'exterminate!'], $action->getResult());
    }

    /**
     * @dataProvider commonTypeProvider
     */
    public function testNonDescribedCommonDataIsSetWithoutAnyModifying(
        string $type,
        string $name,
        $value
    ) {
        $action = new Action(new EmptyActionExample);
        $action->setData($type, $name, $value);
        $this->assertEquals($value, $action->getData($type, $name));
    }

    public function commonTypeProvider(): array
    {
        return [
            [Action::ARGS, 'name', 'Jed'],
            [Action::POST, 'name', 'Kostyak'],
            [Action::FILES, 'avatar', new UploadedFile($this->fileExampleData)]
        ];
    }
}