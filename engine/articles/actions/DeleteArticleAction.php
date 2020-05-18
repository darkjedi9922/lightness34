<?php namespace engine\articles\actions;

use engine\articles\Article;
use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;
use frame\auth\InitAccess;
use frame\stdlib\cash\prev_route;

class DeleteArticleAction extends ActionBody
{
    /** @var Article */
    private $article;

    public function listGet(): array
    {
        return [
            'id' => IntegerField::class
        ];
    }

    public function initialize(array $get)
    {
        $this->article = Article::selectIdentity($get['id']->get());
        InitAccess::accessOneRight('articles', [
            'delete-own' => [$this->article],
            'delete-all' => null
        ]);
    }

    public function succeed(array $post, array $files)
    {
        $this->article->delete();
    }

    public function getSuccessRedirect(): string
    {
        $prevRouter = prev_route::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/articles';
        return '/articles';
    }
}