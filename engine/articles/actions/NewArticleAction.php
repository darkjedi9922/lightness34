<?php namespace engine\articles\actions;

use engine\articles\Article;
use engine\users\cash\user_me;
use frame\actions\ActionBody;
use frame\actions\fields\StringField;
use frame\tools\Init;
use frame\stdlib\cash\prev_router;
use frame\stdlib\configs\JsonConfig;

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
            'title' => StringField::class,
            'text' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        Init::accessRight('articles', 'add');
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        $config = new JsonConfig('config/articles.json');
        /** @var StringField $title */ $title = $post['title'];
        /** @var StringField $text */ $text = $post['text'];

        if ($title->isEmpty()) $errors[] = static::E_NO_TITLE;
        else if ($title->isTooLong($config->{'title.maxLength'})) 
            $errors[] = static::E_LONG_TITLE;

        if ($text->isEmpty()) $errors[] = static::E_NO_TEXT;

        return $errors;
    }

    public function succeed(array $post, array $files)
    {
        $article = new Article;
        $article->title = $post['title']->get();
        $article->content = $post['text']->get();
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