<?php namespace engine\statistics\lists\history;

use frame\lists\paged\PagedList;
use frame\database\Records;
use frame\stdlib\cash\config;
use frame\lists\iterators\IdentityIterator;
use Iterator;
use frame\lists\paged\PagerView;
use frame\stdlib\cash\prev_router;

abstract class HistoryList extends PagedList
{
    private $list;
    private $countAll;

    public function __construct(int $page)
    {
        $identityClass = $this->getStatIdentityClass();
        $this->countAll = Records::from($identityClass::getTable())->count('id');
        $limit = config::get('statistics')->historyListLimit;
        parent::__construct($page, $this->countAll, $limit);

        $this->list = Records::from($identityClass::getTable())
            ->order(['id' => 'DESC'])
            ->range($this->getPager()->getStartMaterialIndex(), $limit)
            ->select();
    }

    public function countOnPage(): int
    {
        return $this->list->count();
    }

    public function getIterator(): Iterator
    {
        return new IdentityIterator($this->list, $this->getStatIdentityClass());
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

    public abstract function getStatIdentityClass(): string;
    protected abstract function assembleArray(IdentityIterator $list): array;
}