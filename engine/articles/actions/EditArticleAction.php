<?php namespace engine\articles\actions;

use engine\articles\Article;
use frame\actions\ActionBody;
use frame\config\Json;
use frame\tools\Init;
use frame\cash\prev_router;

class EditArticleAction extends ActionBody
{
    const E_NO_TITLE = 1;
    const E_NO_TEXT = 2;
    const E_LONG_TITLE = 3;

    /** @var Article */
    private $article;

    public function listGet(): array
    {
        return [
            'id' => self::GET_INT
        ];
    }

    public function listPost(): array
    {
        return [
            'title' => self::POST_TEXT,
            'text' => self::POST_TEXT
        ];
    }

    public function initialize(array $get)
    {
        $this->article = Article::selectIdentity($get['id']);
        Init::accessOneRight('articles', [
            'edit-own' => [$this->article],
            'edit-all' => null
        ]);
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        $config = new Json('config/articles.json');
        $title = $post['title'];
        $text = $post['text'];

        if ($title === '') $errors[] = static::E_NO_TITLE;
        else if (mb_strlen($title) > $config->{'title.maxLength'})
            $errors[] = static::E_LONG_TITLE;

        if ($text === '') $errors[] = static::E_NO_TEXT;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $this->article->title = $post['title'];
        $this->article->content = $post['text'];
        $this->article->update();
    }

    public function getSuccessRedirect(): string
    {
        $prevRouter = prev_router::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/article?id=' . $this->article->id;
        return '/article?id=' . $this->article->id;
    }
}
