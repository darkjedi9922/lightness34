<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\articles\Article;
use engine\users\User;
use frame\cash\pagenumber;
use engine\users\cash\user_me;
use engine\users\Group;
use frame\core\Core;
use frame\views\Pager;
use frame\actions\ViewAction;
use engine\comments\actions\AddComment;
use engine\comments\CommentList;
use frame\tools\JsonEncoder;
use engine\users\cash\my_rights;
use engine\articles\actions\DeleteArticleAction;

$id = (int)Init::requireGet('id');
$article = Article::selectIdentity($id);

Init::require($article !== null);

$me = user_me::get();
$author = User::selectIdentity($article->author_id);
$group = Group::selectIdentity($author->group_id);
$prevPagenumber = pagenumber::get(true);
$page = pagenumber::get();
$moduleId = Core::$app->getModule('articles/comments')->getId();
$materialId = $article->id;
$comments = new CommentList($moduleId, $materialId, $page);
$pages = $comments->getPager()->countPages();
$articleRights = my_rights::get('articles');

$delete = new ViewAction(DeleteArticleAction::class, ['id' => $id]);
$add = new ViewAction(AddComment::class, [
    'module_id' => $moduleId,
    'material_id' => $materialId
]);

$articleCommentsData = [
    'me' => [
        'avatarUrl' => '/' . $me->getAvatarUrl(),
        'login' => $me->login
    ],
    // 'moduleId' => Core::$app->getModule('articles/comments')->getId(),
    // 'materialId' => $article->id,
    'list' => [],
    'pagerHtml' => ($pages > 1 ? (new Pager($comments->getPager(), 'admin'))->getHtml() : ''),
    // 'page' => $page,
    'addUrl' => $add->getUrl()
];

foreach ($comments as $comment) {
    /** @var Comment $comment */
    $commentAuthor = User::selectIdentity($comment->author_id);
    $articleCommentsData['list'][] = [
        'author' => [
            'avatarUrl' => '/' . $commentAuthor->getAvatarUrl(),
            'login' => $commentAuthor->login
        ],
        'date' => date('d.m.Y H:i', $comment->date),
        'text' => $comment->text
    ];
}

$articleCommentsData = JsonEncoder::forHtmlAttribute($articleCommentsData);

$article->setReaded(user_me::get());
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/articles?p=<?= $prevPagenumber ?>" 
            class="breadcrumbs__item breadcrumbs__item--link"
        >Статьи</a>
        <span class="breadcrumbs__divisor"></span>
        <span class="breadcrumbs__item breadcrumbs__item--current">ID <?= $article->id ?></span>
    </div>
    <div class="actions">
        <div class="actions__item">
            <?php if ($articleRights->canOneOf([
                'edit-own' => [$article],
                'edit-all' => null
            ])) : ?>
                <a href="/admin/article/edit?id=<?= $id ?>" class="button">Редактировать</a>
            <?php endif ?>
        </div>
        <div class="actions__item">
            <?php if ($articleRights->canOneOf([
                'delete-own' => [$article],
                'delete-all' => null
            ])) : ?>
                <a href="<?= $delete->getUrl() ?>" class="button button--red">Удалить</a>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="box article">
    <h2 class="article__title"><?= $article->title ?></h2>
    <div class="article__container">
        <div class="author">
            <div class="author__data">
                <img src="/<?= $author->getAvatarUrl() ?>" class="author__avatar">
                <div class="author__info">
                    <a href="/admin/users/profile/<?= $author->login ?>" class="author__login"><?= $author->login ?></a>
                    <span class="author__group"><?= $group->name ?></span>
                </div>
            </div>
            <span class="author__date"><?= date('d.m.Y', $article->date) ?></span>
        </div>
        <p class="article__content"><?= $article->content ?></p>
    </div>
</div>
<div id="article-comments" data-props='<?= $articleCommentsData ?>'></div>