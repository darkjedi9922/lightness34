<?php /** @var frame\views\Page $self */

use cash\pagenumber;
use engine\articles\ArticlePagedList;
use engine\users\User;

use function lightlib\shorten;

$pagenumber = pagenumber::get();
$articles = new ArticlePagedList($pagenumber);

// $ARTICLE_RIGHTS = ArticleObjects::getMyRights();
?>

<div class="content">
    <div class="content-header">
        <span class="content-header__info">Всего записей: <?= $articles->coundAll() ?></span>
        <?php //if ($ARTICLE_RIGHTS->canAdd()): ?>
            <a class="content-header__button" href="/articles/new">Добавить</a>
        <?php //endif ?>
    </div>
    <?php foreach($articles as $article):
        /** @var engine\articles\Article $article */
        $author = User::selectIdentity($article->author_id);
    ?>
        <br><br>
        <div>
            <span class='article-title'><?= $article->title ?></span><br>
            <p><span class="text"><?= shorten($article->content, 450, '...') ?></span></p>
            <p><a href='/article?id=<?=$ARTICLE->get('id') ?>'>Читать полностью...</a></p>
            <p>
                <i>
                    Автор: <a href='/profile?login=<?= $author->login ?>'><?= $author->login ?></a> 
                    | Дата: <?= date('d.m.y H:i', $article->date) ?>
                    | <a href='/article?id=<?= $article->id ?>#comments'>Комментариев: <?= 10 ?></a>
                </i>
            </p>
        </div>
    <?php endforeach ?>
    <?php $articles->getPager()->show('default') ?>
</div>