<?php namespace engine\articles\actions;

use engine\articles\Article;
use frame\actions\ActionBody;
use frame\tools\Init;
use frame\cash\prev_router;

class DeleteArticleAction extends ActionBody
{
    /** @var Article */
    private $article;

    public function listGet(): array
    {
        return [
            'id' => [self::GET_INT, 'The id of the article']
        ];
    }

    public function initialize(array $get)
    {
        $this->article = Article::selectIdentity($get['id']);
        Init::accessOneRight('articles', [
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
        $prevRouter = prev_router::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/articles';
        return '/articles';
    }
}