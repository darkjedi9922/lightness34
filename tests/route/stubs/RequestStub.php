<?php namespace tests\route\stubs;

class RequestStub extends \frame\route\Request
{
    private $request = '';
    private $previous = null;
    private $isAjax = false;

    public function getCurrentRequest(): string
    {
        return $this->request;
    }

    public function setRequest(string $request)
    {
        $this->request = $request;
    }

    public function setPreviousRequest(string $request)
    {
        $this->previous = $request;
    }

    public function getPreviousRequest(): ?string
    {
        return $this->referer ?? null;
    }

    public function setAjax(bool $ajax)
    {
        $this->isAjax = $ajax;
    }

    public function isAjax(): bool
    {
        return $this->isAjax;
    }
}
