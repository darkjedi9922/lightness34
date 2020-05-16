<?php namespace engine\statistics\lists\history;

use frame\lists\paged\PagedList;
use frame\stdlib\cash\config;
use frame\lists\paged\PagerView;
use frame\stdlib\cash\prev_router;
use frame\database\SqlDriver;
use Iterator;

abstract class HistoryList extends PagedList
{
    private $list;
    private $countAll;

    public function __construct(int $page, string $sortField, string $sortOrder)
    {
        $this->countAll = $this->queryCountAll();
        $limit = config::get('statistics')->historyListLimit;
        parent::__construct($page, $this->countAll, $limit);
        $offset = $this->getPager()->getStartMaterialIndex();
        $this->list = SqlDriver::getDriver()->query($this->getSqlQuery(
            $sortField, $sortOrder, $offset, $limit));
    }

    public function countOnPage(): int
    {
        return $this->list->count();
    }

    public function getIterator(): Iterator
    {
        $this->list->seek(0);
        while (($line = $this->list->readLine()) !== null)
            yield $line;
    }

    public function toArray(): array
    {
        $result = $this->assembleArray($this->getIterator());
        $pager = new PagerView($this->getPager(), 'admin');
        $prevRouter = prev_router::get();
        if ($prevRouter) $pager->setMeta('route', $prevRouter->toUrl());
        
        return [
            'list' => $result,
            'countAll' => $this->countAll,
            'pagerHtml' => $pager->getHtml()
        ];
    }

    protected abstract function queryCountAll(): int;
    protected abstract function getSqlQuery(
        string $sortField,
        string $sortOrder,
        int $offset,
        int $limit
    ): string;
    protected abstract function assembleArray(Iterator $list): array;
}