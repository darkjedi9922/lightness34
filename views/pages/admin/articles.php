<?php /** @var frame\views\Page $self */

use frame\cash\pagenumber;
use engine\articles\Article;
use engine\articles\ArticlePagedList;
use engine\users\cash\my_rights;
use engine\users\User;
use function lightlib\shorten;

$pagenumber = pagenumber::get();
$articles = new ArticlePagedList($pagenumber);
$rights = my_rights::get('articles');
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item breadcrumbs__item--current">Статьи</span>
    </div>
</div>
<div class="box">
    <?php if ($articles->countOnPage() == 0) : ?>Статей нет
    <?php else : ?>
        <table width="100%">
            <tr>
                <td><b>ID</b></td>
                <td><b>Название</b></td>
                <td><b>Автор</b></td>
                <td><b>Дата</b></td>
            </tr>
            <?php foreach ($articles as $article):
                /** @var Article $article */
                $author = User::selectIdentity($article->author_id);
            ?>
                <tr>
                    <td><?= $article->id ?></td>
                    <td><a class="link" href="/admin/article?id=<?= $article->id ?>"><?= shorten($article->title, 80) ?></a></td>
                    <td><a class="link" href="/admin/users/profile/<?= $author->login ?>"><?= $author->login ?></a></td>
                    <td><?= date('d.m.Y H:i', $article->date) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <?php $articles->getPager()->show('admin') ?>
    <?php endif ?>
</div>