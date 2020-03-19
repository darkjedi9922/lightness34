<?php /** @var frame\views\Page $self */

use frame\stdlib\cash\pagenumber;
use engine\articles\ArticlePagedList;
use engine\comments\Comment;
use engine\users\cash\my_rights;
use engine\users\User;

use function lightlib\shorten;

$pagenumber = pagenumber::get();
$articles = new ArticlePagedList($pagenumber);
$rights = my_rights::get('articles');
?>

<div class="content">
    <div class="content-header">
        <b><span class="content-header__info">Всего записей: <?= $articles->countAll() ?></span></b>
        <?php if ($rights->can('add')): ?>
            <a class="content-header__button" href="/articles/new">Добавить</a>
        <?php endif ?>
    </div>
    <?php foreach($articles as $article):
        /** @var engine\articles\Article $article */
        $author = User::selectIdentity($article->author_id);
        $comments = Comment::count('articles', $article->id);
    ?>
        <br>
        <div class="article">
            <span class='article__title'><?= $article->title ?></span><br>
            <p><span class="article__text"><?= shorten($article->content, 450, '...') ?></span></p>
            <p><a class="link" href='/article?id=<?= $article->id ?>'>Читать полностью...</a></p>
            <p class="article__meta">
                Автор: <a class="link" href='/profile?login=<?= $author->login ?>'><?= $author->login ?></a> 
                | Дата: <?= date('d.m.y H:i', $article->date) ?>
                | <a class="link" href='/article?id=<?= $article->id ?>#comments'>Комментариев: <?= $comments ?></a>
            </p>
        </div>
    <?php endforeach ?>
    <?php $articles->getPager()->show('default') ?>
</div>