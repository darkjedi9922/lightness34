<?php namespace engine\articles\actions;

use engine\articles\Article;
use engine\users\cash\user_me;
use frame\actions\Action;
use frame\config\Json;
use frame\tools\Init;

/**
 * Права: добавление статей
 * 
 * Данные:
 * title: название статьи
 * text: текст статьи
 */
class NewArticleAction extends Action
{
    const E_NO_TITLE = 1;
    const E_NO_TEXT = 2;
    const E_LONG_TITLE = 3;

    private $id;

    protected function initialization()
    {
        Init::accessRight('articles', 'add');
    }

    protected function validate(array $post, array $files): array
    {
        $errors = [];
        $config = new Json('config/articles.json');
        $title = $this->getData('post', 'title');
        $text = $this->getData('post', 'text');

        if (!$title) $errors[] = static::E_NO_TITLE;
        else if (mb_strlen($title) > $config->{'title.maxLength'}) 
            $errors[] = static::E_LONG_TITLE;

        if (!$text) $errors[] = static::E_NO_TEXT;

        return $errors;
    }

    protected function succeed(array $post, array $files)
    {
        $article = new Article;
        $article->title = $this->getData('post', 'title');
        $article->content = $this->getData('post', 'text');
        $article->author_id = user_me::get()->id;
        $article->date = time();
        $this->id = $article->insert();
    }

    public function getSuccessRedirect(): string
    {
        // return '/article?id='.$this->id;
        return '/articles';
    }

    protected function getPostToSave(): array
    {
        return ['title', 'text'];
    }
}