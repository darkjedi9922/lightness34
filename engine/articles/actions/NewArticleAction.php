<?php namespace engine\articles\actions;

use engine\articles\Article;
use engine\users\cash\user_me;
use frame\actions\ActionBody;
use frame\config\Json;
use frame\tools\Init;
use frame\cash\prev_router;

/**
 * Права: добавление статей
 * 
 * Данные:
 * title: название статьи
 * text: текст статьи
 */
class NewArticleAction extends ActionBody
{
    const E_NO_TITLE = 1;
    const E_NO_TEXT = 2;
    const E_LONG_TITLE = 3;

    private $id;

    public function listPost(): array
    {
        return [
            'title' => self::POST_TEXT,
            'text' => self::POST_TEXT
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('articles', 'add');
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
        $article = new Article;
        $article->title = $post['title'];
        $article->content = $post['text'];
        $article->author_id = user_me::get()->id;
        $article->date = time();
        $this->id = $article->insert();
    }

    public function getSuccessRedirect(): string
    {
        $prevRouter = prev_router::get();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/article?id=' . $this->id;
        return '/article?id=' . $this->id;
    }
}