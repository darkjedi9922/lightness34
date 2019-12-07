<?php /** @var frame\views\Page $self */

use frame\tools\Init;
use engine\users\cash\user_me;
use engine\users\User;
use engine\users\Group;

$me = user_me::get();
Init::access($me->group_id !== Group::GUEST_ID);
$withId = (int)Init::requireGet('with');
$with = User::selectIdentity($withId);
Init::require($with !== null);

$self->setLayout('admin');
?>

<div id="message-list"></div>