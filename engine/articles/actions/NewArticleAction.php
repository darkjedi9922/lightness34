<?php namespace engine\articles\actions;

use engine\articles\Article;
use engine\users\User;
use frame\actions\ActionBody;
use frame\actions\fields\StringField;
use frame\auth\InitAccess;
use frame\route\Router;
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
        InitAccess::accessRight('articles', 'add');
    }

    public function validate(array $post, array $files): array
    {
        $errors = [];
        $config = new JsonConfig('config/articles');
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
        $article->author_id = User::getMe()->id;
        $article->date = time();
        $this->id = $article->insert();
    }

    public function getSuccessRedirect(): string
    {
        $prevRouter = Router::getDriver()->getPreviousRoute();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/article?id=' . $this->id;
        return '/article?id=' . $this->id;
    }
}