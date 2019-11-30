<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\articles\Article;
use engine\users\User;
use cash\pagenumber;
use engine\users\cash\user_me;

$self->setLayout('admin');

$id = (int)Init::requireGet('id');
$article = Article::selectIdentity($id);

Init::require($article !== null);

$author = User::selectIdentity($article->author_id);
$prevPagenumber = pagenumber::get(true);

$article->setReaded(user_me::get());
?>

<div class="box">
    <h3><a href="/admin/articles?p=<?= $prevPagenumber ?>">Статьи</a></h3><br>
    <h2><?= $article->title ?></h2>
    <p class="content-text" style="font-size:12pt"><?= $article->content ?></p>
    <hr>
    Добавил: <a href="/admin/users/profile?login=<?= $author->login ?>"><?= $author->login ?></a>
    (<?= date('d.m.Y H:i', $article->date) ?>)
</div>