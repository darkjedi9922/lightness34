<?php /** @var frame\views\Page $self */

use frame\lists\paged\PagerModel;
use engine\articles\Article;
use engine\users\User;
use function lightlib\shorten;
use frame\tools\JsonEncoder;
use frame\auth\InitAccess;
use frame\config\ConfigRouter;
use frame\database\Records;
use frame\lists\base\IdentityList;

InitAccess::accessRight('articles', 'see-list');
$pagenumber = PagerModel::getRoutePage();
$articleCount = Records::from(Article::getTable())->count('id');
$articleLimit = ConfigRouter::getDriver()->findConfig('articles')->get('list.amount');
$pager = new PagerModel($pagenumber, $articleCount, $articleLimit);
$articles = new IdentityList(Article::class, ['id' => 'DESC'], $pager->getOffset(), $pager->getLimit());
$rights = User::getMyRights('articles');

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
        <span class="breadcrumbs__item breadcrumbs__item--current">
            Статьи (<?= $articles->count() ?>)
        </span>
    </div>
    <div class="actions">
        <?php if ($pager->countPages() > 1) : ?>
            <div class="actions__item">
                <?php $pager->show('admin') ?>
            </div>
        <?php endif ?>
        <?php if ($rights->can('add')): ?>
            <div class="actions__item">
                <a href="/admin/article/new" class="button">Добавить статью</a>
            </div>
        <?php endif ?>
    </div>
</div>
<div id="articles" data-props="<?= $tableProps ?>"></div>