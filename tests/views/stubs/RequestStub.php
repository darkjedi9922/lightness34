<?php namespace tests\views\stubs;

class RequestStub extends \frame\route\Request
{
    private $request = '';
    private $referer = null;
    private $isAjax = false;

    public function getRequest(): string
    {
        return $this->request;
    }

    public function setRequest(string $request)
    {
        $this->request = $request;
    }

    public function setReferer(string $referer)
    {
        $this->referer = $referer;
    }

    public function getReferer(): string
    {
        if ($this->referer === null) throw new \Exception;
        return $this->referer;
    }

    public function hasReferer(): bool
    {
        return $this->referer !== null;
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
