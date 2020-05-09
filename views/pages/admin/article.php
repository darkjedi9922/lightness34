<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\articles\Article;
use engine\users\User;
use frame\stdlib\cash\pagenumber;
use engine\users\cash\user_me;
use engine\users\Group;
use frame\modules\Modules;
use frame\lists\paged\PagerView;
use frame\actions\ViewAction;
use engine\comments\actions\AddComment;
use engine\comments\CommentList;
use engine\comments\Comment;
use frame\tools\JsonEncoder;
use engine\users\cash\my_rights;
use engine\articles\actions\DeleteArticleAction;
use engine\comments\actions\DeleteComment;

$id = (int)Init::requireGet('id');
$article = Article::selectIdentity($id);

Init::require($article !== null);

$me = user_me::get();
$author = User::selectIdentity($article->author_id);
$group = Group::selectIdentity($author->group_id);
$prevPagenumber = pagenumber::get(true);
$page = pagenumber::get();
$moduleId = Modules::getDriver()->findByName('articles/comments')->getId();
$materialId = $article->id;
$comments = new CommentList($moduleId, $materialId, $page);
$pages = $comments->getPager()->countPages();
$articleRights = my_rights::get('articles');

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
        'isOnline' => (bool) $author->online
    ],
    'text' => $article->content,
    'date' => date('d.m.Y H:i', $article->date)
];

$articleCommentsData = [
    'me' => [
        'avatarUrl' => '/' . $me->getAvatarUrl(),
        'login' => $me->login
    ],
    // 'moduleId' => Modules::getDriver()->findByName('articles/comments')->getId(),
    // 'materialId' => $article->id,
    'list' => [],
    'pagerHtml' => ($pages > 1 ? (new PagerView($comments->getPager(), 'admin'))->getHtml() : ''),
    // 'page' => $page,
    'addUrl' => $add->getUrl()
];

$commentRights = my_rights::get('articles/comments');
$deleteComment = new ViewAction(DeleteComment::class);
foreach ($comments as $comment) {
    /** @var Comment $comment */
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
            'isOnline' => (bool) $commentAuthor->online
        ],
        'date' => date('d.m.Y H:i', $comment->date),
        'text' => $comment->text,
        'isNew' => $comment->isNewFor($me),
        'deleteUrl' => $deleteCommentUrl
    ];

    $comment->setReadedFor($me);
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
<div id="article-page" data-props="<?= JsonEncoder::forHtmlAttribute($articleProps) ?>"></div>
<div id="article-comments" data-props='<?= $articleCommentsData ?>'></div>