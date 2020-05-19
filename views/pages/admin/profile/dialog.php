<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\route\InitRoute;
use engine\users\User;
use frame\lists\paged\PagerModel;
use frame\tools\JsonEncoder;

InitAccess::accessRight('messages', 'use');
$withId = (int)InitRoute::requireGet('uid');
$with = User::selectIdentity($withId);
InitRoute::require($with !== null);
$me = User::getMe();

$pageProps = [
    'me' => [
        'id' => $me->id,
        'login' => $me->login
    ],
    'user' => [
        'id' => $with->id,
        'login' => $with->login
    ],
    'pagenumber' => PagerModel::getRoutePage()
];
$pageProps = JsonEncoder::forHtmlAttribute($pageProps);
?>

<div id="messages-page" data-props="<?= $pageProps ?>"></div>