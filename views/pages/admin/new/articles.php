<?php /** @var frame\views\Page $self */

use frame\stdlib\cash\pagenumber;
use engine\articles\NewArticlePagedList;
use engine\articles\Article;
use engine\users\User;
use frame\auth\InitAccess;
use frame\tools\JsonEncoder;
use function lightlib\shorten;

InitAccess::accessRight('articles', 'see-new-list');
$pagenumber = pagenumber::get();
$articles = new NewArticlePagedList($pagenumber);

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
$tableProps = JsonEncoder::forHtmlAttribute($tableProps);
?>

<div class="content__header">
    <div class="breadcrumbs">
        <span class="breadcrumbs__item">Новое</span>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">
            Статьи (<?= $articles->countAll() ?>)
        </span>
    </div>
    <div class="actions">
        <?php if ($articles->getPager()->countPages() > 1) : ?>
            <div class="actions__item">
                <?php $articles->getPager()->show('admin') ?>
            </div>
        <?php endif ?>
    </div>
</div>
<div id="articles" data-props="<?= $tableProps ?>"></div>