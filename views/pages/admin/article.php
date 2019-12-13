<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\articles\Article;
use engine\users\User;
use frame\cash\pagenumber;
use engine\users\cash\user_me;
use engine\users\Group;

$self->setLayout('admin');

$id = (int)Init::requireGet('id');
$article = Article::selectIdentity($id);

Init::require($article !== null);

$author = User::selectIdentity($article->author_id);
$group = Group::selectIdentity($author->group_id);
$prevPagenumber = pagenumber::get(true);

$article->setReaded(user_me::get());
?>

<div class="breadcrumbs">
    <a href="/admin/articles?p=<?= $prevPagenumber ?>" 
        class="breadcrumbs__item breadcrumbs__item--link"
    >Статьи</a>
    <span class="breadcrumbs__divisor"></span>
    <span class="breadcrumbs__item breadcrumbs__item--current">ID <?= $article->id ?></span>
</div>
<div class="box article">
    <h2 class="article__title"><?= $article->title ?></h2>
    <div class="article__container">
        <div class="author">
            <div class="author__data">
                <img src="/<?= $author->getAvatarUrl() ?>" class="author__avatar">
                <div class="author__info">
                    <a href="/admin/users/profile?login=<?= $author->login ?>" class="author__login"><?= $author->login ?></a>
                    <span class="author__group"><?= $group->name ?></span>
                </div>
            </div>
            <span class="author__date"><?= date('d.m.Y', $article->date) ?></span>
        </div>
        <p class="article__content"><?= $article->content ?></p>
    </div>
</div>