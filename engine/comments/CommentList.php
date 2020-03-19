<?php namespace engine\comments;

use frame\lists\paged\IdentityPagedList;
use frame\stdlib\cash\config;

class CommentList extends IdentityPagedList
{
    private $moduleId;
    private $materialId;

    public function __construct(int $moduleId, int $materialId, int $page)
    {
        $this->moduleId = $moduleId;
        $this->materialId = $materialId;
        parent::__construct($page);
    }

    public function getIdentityClass(): string
    {
        return Comment::class;
    }

    public function getWhere(): array
    {
        return [
            'module_id' => $this->moduleId,
            'material_id' => $this->materialId
        ];
    }

    protected function loadPageLimit(): int
    {
        return config::get('comments')->{'list.amount'};
    }
}