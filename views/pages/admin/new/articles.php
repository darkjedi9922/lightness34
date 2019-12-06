<?php /** @var frame\views\Page $self */

use frame\cash\pagenumber;
use engine\articles\NewArticlePagedList;
use engine\users\User;
use function lightlib\shorten;

$pagenumber = pagenumber::get();
$articles = new NewArticlePagedList($pagenumber);

$self->setLayout('admin');
?>

<div class="box">
    <h2>Новые статьи</h2>
    <?php if ($articles->countAll() == 0) : ?>Новых статей нет
    <?php else : ?>
        <table width="100%">
            <tr>
                <td><b>ID</b></td>
                <td><b>Название</b></td>
                <td><b>Автор</b></td>
                <td><b>Дата</b></td>
            </tr>
            <?php foreach ($articles as $article): /** @var engine\articles\Article $article */ ?>
            <?php $author = User::selectIdentity($article->author_id) ?>
                <tr>
                    <td><?= $article->id ?></td>
                    <td><a href="/admin/article?id=<?= $article->id ?>"><?= shorten($article->title, 80, '...') ?></a></td>
                    <td><a href="/admin/users/profile/<?= $author->login ?>"><?= $author->login ?></a></td>
                    <td><?= date('d.m.Y H:i', $article->date) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <?php $articles->getPager()->show('admin') ?>
    <?php endif ?>
</div>