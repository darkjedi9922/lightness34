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

Init::accessRight('articles/comments', 'see-new-list');
$pagenumber = pagenumber::get();
$items = new NewCommentPagedList($pagenumber);
$me = user_me::get();

$commentListProps = [];
$setReaded = config::get('comments')->{'new.setReadedOnNewsPage'};
foreach ($items as $comment) {
    /** @var Comment $comment */
    $author = User::selectIdentity($comment->author_id);
    $module = Modules::getDriver()->findById($comment->module_id);
    $commentListProps[] = [
        'moduleName' => $module ? $module->getName() : null,
        'materialId' => $comment->material_id,
        'author' => [
            'login' => $author->login,
            'avatarUrl' => '/' . $author->getAvatarUrl()
        ],
        'date' => date('d.m.Y H:i', $comment->date),
        'text' => $comment->text
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