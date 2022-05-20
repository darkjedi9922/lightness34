<?php /** @var frame\views\Page $self */

use frame\route\InitRoute;
use engine\articles\Article;
use engine\users\User;
use frame\lists\paged\PagerModel;
use engine\users\Group;
use frame\modules\Modules;
use frame\lists\paged\PagerView;
use frame\actions\ViewAction;
use engine\comments\actions\AddComment;
use engine\comments\Comment;
use frame\tools\JsonEncoder;
use engine\articles\actions\DeleteArticleAction;
use engine\comments\actions\DeleteComment;
use frame\config\ConfigRouter;
use frame\database\Records;

$id = (int)InitRoute::requireGet('id');
$article = Article::selectIdentity($id);

InitRoute::require($article !== null);

$me = User::getMe();
$author = User::selectIdentity($article->author_id);
$group = Group::selectIdentity($author->group_id);
$prevPagenumber = PagerModel::getRoutePage(true);
$page = PagerModel::getRoutePage();
$moduleId = Modules::getDriver()->findByName('articles/comments')->getId();
$materialId = $article->id;
$conditions = ['module_id' => $moduleId, 'material_id' => $materialId];
$commentCount = Records::from(Comment::getTable(), $conditions)->count('id');
$commentConfig = ConfigRouter::getDriver()->findConfig('comments');
$pageLimit = $commentConfig->get('list.amount');
$pager = new PagerModel($page, $commentCount, $pageLimit);
$comments = Records::from(Comment::getTable(), $conditions)
    ->range($pager->getOffset(), $pager->getLimit())
    ->order(['id' => $commentConfig->get('list.order')])->select();
$pages = $pager->countPages();
$articleRights = User::getMyRights('articles');

$delete = new ViewAction(DeleteArticleAction::class, ['id' => $id]);
$add = new ViewAction(AddComment::class, [
    'module_id' => $moduleId,
    'material_id' => $materialId
]);

$articleProps = [
    'title' => $article->title,
    'author' => [
        'login' => $author->login,
        'avatarUrl' => '/' . $author->getAvatarUrl(),
        'isOnline' => (bool)$author->online
    ],
    'text' => $article->content,
    'date' => date('d.m.Y H:i', $article->date)
];

$articleCommentsData = [
    'me' => [
        'avatarUrl' => '/' . $me->getAvatarUrl(),
        'login' => $me->login,
        'isOnline' => (bool)$me->online
    ],
    'list' => [],
    'pagerHtml' => ($pages > 1 ? (new PagerView($pager, 'admin'))->getHtml() : ''),
    'addUrl' => $add->getUrl()
];

$commentRights = User::getMyRights('articles/comments');
$deleteComment = new ViewAction(DeleteComment::class);
while ($comment = $comments->readLine()) {
    $comment = new Comment($comment);
    $commentAuthor = User::selectIdentity($comment->author_id);

    if ($commentRights->canOneOf([
        'delete-own' => [$comment],
        'delete-all' => null
    ])) {
        $deleteComment->setArg('id', $comment->id);
        $deleteCommentUrl = $deleteComment->getUrl();
    } else {
        $deleteCommentUrl = null;
    }

    $articleCommentsData['list'][] = [
        'author' => [
            'avatarUrl' => '/' . $commentAuthor->getAvatarUrl(),
            'login' => $commentAuthor->login,
            'isOnline' => (bool)$commentAuthor->online
        ],
        'date' => date('d.m.Y H:i', $comment->date),
        'text' => $comment->text,
        'isNew' => $comment->isNewFor($me),
        'deleteUrl' => $deleteCommentUrl
    ];

    $comment->setReadedFor($me);
}

$articleCommentsData = JsonEncoder::forHtmlAttribute($articleCommentsData);

$article->setReaded(User::getMe());
?>

<div class="content__header">
    <div class="breadcrumbs">
        <a href="/admin/articles?p=<?= $prevPagenumber ?>" class="breadcrumbs__item breadcrumbs__item--link">Статьи</a>
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
<div id="article-page" data-props="<?= JsonEncoder::forHtmlAttribute($articleProps) ?>"></div>
<div id="article-comments" data-props='<?= $articleCommentsData ?>'></div>