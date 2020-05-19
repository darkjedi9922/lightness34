<?php namespace engine\articles\actions;

use engine\articles\Article;
use frame\actions\ActionBody;
use frame\actions\fields\IntegerField;
use frame\actions\fields\StringField;
use frame\auth\InitAccess;
use frame\route\Router;
use frame\stdlib\configs\JsonConfig;

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
            'id' => IntegerField::class
        ];
    }

    public function listPost(): array
    {
        return [
            'title' => StringField::class,
            'text' => StringField::class
        ];
    }

    public function initialize(array $get)
    {
        $this->article = Article::selectIdentity($get['id']->get());
        InitAccess::accessOneRight('articles', [
            'edit-own' => [$this->article],
            'edit-all' => null
        ]);
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
        $this->article->title = $post['title']->get();
        $this->article->content = $post['text']->get();
        $this->article->update();
    }

    public function getSuccessRedirect(): string
    {
        $prevRouter = Router::getDriver()->getPreviousRoute();
        if ($prevRouter && $prevRouter->isInNamespace('admin'))
            return '/admin/article?id=' . $this->article->id;
        return '/article?id=' . $this->article->id;
    }
}
