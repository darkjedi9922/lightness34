<?php /** @var frame\views\DynamicPage $self */

use engine\users\User;
use frame\tools\Init;
use engine\users\Gender;
use engine\users\Group;
use engine\users\cash\my_rights;
use engine\users\cash\user_me;
use engine\users\actions\DeleteAvatarAction;
use frame\actions\ViewAction;
use frame\tools\JsonEncoder;

Init::require(count($self->getArguments()) === 1);

$login = $self->getArgument(0);
$profile = User::select(['login' => $login]);

Init::require($profile !== null);

$gender = Gender::selectIdentity($profile->gender_id);
$group = Group::selectIdentity($profile->group_id);
$rights = my_rights::get('users');
$me = user_me::get();

$deleteAvatar = new ViewAction(DeleteAvatarAction::class, ['uid' => $profile->id]);

$pageProps = [
    'user' => [
        'id' => $profile->id,
        'login' => $profile->login,
        'hasAvatar' => $profile->hasAvatar(),
        'avatarUrl' => '/' . $profile->getAvatarUrl(),
        'name' => $profile->name,
        'surname' => $profile->surname,
        'gender' => $gender->name,
        'email' => $profile->email,
        'group' => $group->name,
        'registrationDate' => date('d.m.Y H:i', $profile->registration_date),
        'lastOnlineTime' => $profile->last_online_time
            ? date('d.m.Y H:i', $profile->last_online_time)
            : null,
        'lastUserAgent' => $profile->last_user_agent,
        'isOnline' => $profile->online === 1
    ],
    'rights' => [
        'canEdit' => $rights->canOneOf([
            'edit-all' => [$profile],
            'edit-own' => [$profile]
        ]),
        'canChangeGroup' => $me->group_id === Group::ROOT_ID 
            && $group->id !== Group::ROOT_ID
    ],
    'actions' => [
        'deleteAvatarUrl' => $deleteAvatar->getUrl()
    ]
];
$pageProps = JsonEncoder::forHtmlAttribute($pageProps);
?>

<div id="profile-page" data-props="<?= $pageProps ?>"></div>