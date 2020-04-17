<?php namespace tests\route\stubs;

use frame\stdlib\cash\router;

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

        // Между тестами в кэше может оставаться роутер с предыдущим URL.
        // Чтобы не запускать для каждого теста отдельный процесс, сами 
        // на всякий заменим его. 
        router::get()->setUrl($request);
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
