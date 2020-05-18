<?php /** @var frame\views\Page $self */

use frame\auth\InitAccess;
use frame\route\InitRoute;
use engine\users\cash\user_me;
use engine\users\User;
use frame\stdlib\cash\pagenumber;
use frame\tools\JsonEncoder;

InitAccess::accessRight('messages', 'use');
$withId = (int)InitRoute::requireGet('uid');
$with = User::selectIdentity($withId);
InitRoute::require($with !== null);
$me = user_me::get();

$pageProps = [
    'me' => [
        'id' => $me->id,
        'login' => $me->login
    ],
    'user' => [
        'id' => $with->id,
        'login' => $with->login
    ],
    'pagenumber' => pagenumber::get()
];
$pageProps = JsonEncoder::forHtmlAttribute($pageProps);
?>

<div id="messages-page" data-props="<?= $pageProps ?>"></div>