<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\articles\Article;
use engine\users\User;
use frame\cash\pagenumber;
use engine\users\cash\user_me;
use engine\users\Group;
use frame\Core;
use frame\views\Pager;
use frame\actions\ViewAction;
use engine\comments\actions\AddComment;
use engine\comments\CommentList;

$id = (int)Init::requireGet('id');
$article = Article::selectIdentity($id);

Init::require($article !== null);

$me = user_me::get();
$author = User::selectIdentity($article->author_id);
$group = Group::selectIdentity($author->group_id);
$prevPagenumber = pagenumber::get(true);
$page = pagenumber::get();
$moduleId = Core::$app->getModule('article-comments')->getId();
$materialId = $article->id;
$comments = new CommentList($moduleId, $materialId, $page);
$pages = $comments->getPager()->countPages();

$add = new ViewAction(AddComment::class, [
    'module_id' => $moduleId,
    'material_id' => $materialId
]);

$articleCommentsData = [
    'me' => [
        'avatarUrl' => '/' . $me->getAvatarUrl(),
        'login' => $me->login
    ],
    // 'moduleId' => Core::$app->getModule('article-comments')->getId(),
    // 'materialId' => $article->id,
    'list' => [],
    'pagerHtml' => ($pages > 1 ? (new Pager($comments->getPager(), 'admin'))->getHtml() : ''),
    // 'page' => $page,
    'addUrl' => $add->getUrl()
];

foreach ($comments as $comment) {
    /** @var Comment $comment */
    $author = User::selectIdentity($comment->author_id);
    $articleCommentsData['list'][] = [
        'author' => [
            'avatarUrl' => '/' . $author->getAvatarUrl(),
            'login' => $author->login
        ],
        'date' => date('d.m.Y H:i', $comment->date),
        'text' => $comment->text
    ];
}

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
</div>
<div class="box article">
    <h2 class="article__title"><?= $article->title ?></h2>
    <div class="article__container">
        <div class="author">
            <div class="author__data">
                <img src="/<?= $author->getAvatarUrl() ?>" class="author__avatar">
                <div class="author__info">
                    <a href="/admin/users/profile?login=<?= $author->login ?>" class="author__login"><?= $author->login ?></a>
                    <span class="author__group"><?= $group->name ?></span>
                </div>
            </div>
            <span class="author__date"><?= date('d.m.Y', $article->date) ?></span>
        </div>
        <p class="article__content"><?= $article->content ?></p>
    </div>
</div>
<span class="content__title">Комментарии</span>

<div id="article-comments" data-props='<?= json_encode($articleCommentsData, JSON_HEX_AMP) ?>'></div>