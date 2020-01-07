<?php /** @var frame\views\Page $self */

use frame\cash\pagenumber;
use engine\articles\Article;
use engine\articles\ArticlePagedList;
use engine\users\cash\my_rights;
use engine\users\User;
use function lightlib\shorten;
use frame\tools\JsonEncoder;

$pagenumber = pagenumber::get();
$articles = new ArticlePagedList($pagenumber);
$rights = my_rights::get('articles');

$tableProps = ['items' => []];
foreach ($articles as $article) {
    /** @var Article $article */
    $author = User::selectIdentity($article->author_id);
    $item = [
        'id' => $article->id,
        'title' => shorten($article->title, 80, '...'),
        'author' => $author->login,
        'date' => date('d.m.Y H:i', $article->date)
    ];
    $tableProps['items'][] = $item;
}
$jsonEncoder = new JsonEncoder;
$tableProps = $jsonEncoder->forHtmlAttribute($tableProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item breadcrumbs__item--current">Статьи</span>
    </div>
    <div class="box box--headed">
        <?php $articles->getPager()->show('admin') ?>
    </div>
</div>
<div id="articles" data-props="<?= $tableProps ?>"></div>