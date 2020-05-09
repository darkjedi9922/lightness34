<?php /** @var frame\views\Page $self */

use frame\stdlib\cash\pagenumber;
use engine\comments\NewCommentPagedList;
use engine\comments\Comment;
use engine\users\User;
use frame\tools\Init;
use frame\tools\JsonEncoder;
use frame\modules\Modules;
use frame\stdlib\cash\config;
use engine\users\cash\user_me;
use engine\users\cash\my_rights;
use frame\actions\ViewAction;
use engine\comments\actions\DeleteComment;

Init::accessRight('articles/comments', 'see-new-list');
$pagenumber = pagenumber::get();
$items = new NewCommentPagedList($pagenumber);
$me = user_me::get();

$commentListProps = [];
$setReaded = config::get('comments')->{'new.setReadedOnNewsPage'};
$deleteComment = new ViewAction(DeleteComment::class);
foreach ($items as $comment) {
    /** @var Comment $comment */
    $author = User::selectIdentity($comment->author_id);
    $module = Modules::getDriver()->findById($comment->module_id);

    if (my_rights::get($module->getName())->canOneOf([
        'delete-own' => [$comment],
        'delete-all' => null
    ])) {
        $deleteComment->setArg('id', $comment->id);
        $deleteUrl = $deleteComment->getUrl();
    } else {
        $deleteUrl = null;
    }

    $commentListProps[] = [
        'moduleName' => $module ? $module->getName() : null,
        'materialId' => $comment->material_id,
        'author' => [
            'login' => $author->login,
            'avatarUrl' => '/' . $author->getAvatarUrl()
        ],
        'date' => date('d.m.Y H:i', $comment->date),
        'text' => $comment->text,
        'deleteUrl' => $deleteUrl
    ];

    if ($setReaded) $comment->setReadedFor($me);
}

$props = JsonEncoder::forHtmlAttribute([
    'countAll' => $items->countAll(),
    'pagerHtml' => ($items->getPager()->countPages() > 1
        ? (new PagerView($items->getPager(), 'admin'))->getHtml() : ''),
    'comments' => $commentListProps
])
?>

<div id="new-comments" data-props="<?= $props ?>"></div>