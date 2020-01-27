<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\Group;
use engine\users\cash\user_me;
use engine\users\User;
use frame\cash\pagenumber;
use frame\tools\JsonEncoder;

$withId = (int) Init::requireGet('uid');
$with = User::selectIdentity($withId);
Init::require($with !== null);
$me = user_me::get();
Init::access($me->group_id !== Group::GUEST_ID);

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