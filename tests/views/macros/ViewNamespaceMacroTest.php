<?php
use PHPUnit\Framework\TestCase;
use frame\views\View;
use tests\views\examples\ViewNamespaceMacroExample;

class ViewNamespaceMacroTest extends TestCase
{
    public function testRunsWhenSpecifiedNamespaceMatches()
    {
        $view = new View('tree/twiced/leaf');
        $macro = new ViewNamespaceMacroExample('tree/twiced');
    
        $this->assertFalse($macro->isRun);
        $macro->exec($view);
        $this->assertTrue($macro->isRun);
    }

    public function testDoesNotRunWhenSpecifiedNamespaceDoesNotMatch()
    {
        $view = new View('tree/twiced/leaf');
        $macro = new ViewNamespaceMacroExample('flower/twiced');

        $this->assertFalse($macro->isRun);
        $macro->exec($view);
        $this->assertFalse($macro->isRun);
    }
}