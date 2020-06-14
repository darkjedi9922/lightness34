<?php /** @var frame\views\Block $self */

use engine\users\User;
use engine\users\Group;

$adminRights = User::getMyRights('admin');
$usersRights = User::getMyRights('users');
$messageRights = User::getMyRights('messages');
$articleRights = User::getMyRights('articles');
$commentsRights = User::getMyRights('articles/comments');
$statsRights = User::getMyRights('stat');
$isIRoot = User::getMe()->group_id === Group::ROOT_ID;

$globalItems = [];

$globalItems[] = [
    'name' => 'Главная',
    'link' => '/admin/home',
    'icon' => 'home'
];

if ($usersRights->can('see-list')) $globalItems[] = [
    'name' => 'Пользователи',
    'link' => '/admin/users',
    'icon' => 'user'
];

if ($articleRights->can('see-list')) $globalItems[] = [
    'name' => 'Статьи',
    'link' => '/admin/articles',
    'icon' => 'doc-text'
];

$newsItems = [];

if ($articleRights->can('see-new-list')) $newsItems[] = [
    'name' => 'Статьи',
    'link' => '/admin/new/articles',
    'icon' => 'doc-text'
];

if ($commentsRights->can('see-new-list')) $newsItems[] = [
    'name' => 'Комментарии',
    'link' => '/admin/new/comments',
    'icon' => 'commenting'
];

if (!empty($newsItems)) $globalItems[] = [
    'name' => 'Новое',
    'icon' => 'rss',
    'submenu' => $newsItems
];

$settingsItems = [];

if ($isIRoot) $settingsItems[] = [
    'name' => 'Общие',
    'icon' => 'sliders',
    'link' => '/admin/settings/core'
];

$userSettingsItems = [];

if ($usersRights->can('setup')) $userSettingsItems[] = [
    'name' => 'Общие',
    'icon' => 'cog',
    'link' => '/admin/settings/users'
];
if ($isIRoot) $userSettingsItems[] = [
    'name' => 'Группы',
    'icon' => 'id-card-o',
    'link' => '/admin/users/groups'
];
if ($usersRights->can('configure-genders')) $userSettingsItems[] = [
    'name' => 'Пол',
    'icon' => 'transgender',
    'link' => '/admin/users/genders'
];
if ($messageRights->can('setup')) $userSettingsItems[] = [
    'name' => 'Сообщения',
    'icon' => 'email',
    'link' => '/admin/settings/messages'
];

if (!empty($userSettingsItems)) $settingsItems[] = [
    'name' => 'Пользователи',
    'icon' => 'user',
    'submenu' => $userSettingsItems
];

if ($articleRights->can('configure')) $settingsItems[] = [
    'name' => 'Статьи',
    'icon' => 'doc-text',
    'link' => '/admin/settings/articles'
];
if ($commentsRights->can('configure')) $settingsItems[] = [
    'name' => 'Комментарии',
    'icon' => 'commenting',
    'link' => '/admin/settings/comments'
];
if ($statsRights->can('configure')) $settingsItems[] = [
    'name' => 'Мониторинг',
    'icon' => 'chart-bar',
    'link' => '/admin/settings/statistics'
];
if ($isIRoot) $settingsItems[] = [
    'name' => 'Админ-панель',
    'icon' => 'television',
    'link' => '/admin/settings/admin'
];

if (!empty($settingsItems)) $globalItems[] = [
    'name' => 'Настройки',
    'icon' => 'cog',
    'submenu' => $settingsItems
];

if ($statsRights->can('see')) $globalItems[] = [
    'name' => 'Мониторинг',
    'icon' => 'chart-bar',
    'submenu' => [[
        'name' => 'Маршруты',
        'icon' => 'link',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/routes/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/routes/charts'
        ]]
    ], [
        'name' => 'События',
        'icon' => 'flash-1',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/events/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/events/charts'
        ]]
    ], [
        'name' => 'Модули',
        'icon' => 'cube',
        'link' => '/admin/statistics/modules'
    ], [
        'name' => 'Представления',
        'icon' => 'television',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/views/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/views/charts'
        ]],
    ], [
        'name' => 'Действия',
        'icon' => 'superpowers',
        'submenu' => [[
            'name' => 'Каталог',
            'icon' => 'folder',
            'link' => '/admin/statistics/actions/catalog'
        ], [
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/actions/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/actions/charts'
        ]]
    ], [
        'name' => 'База данных',
        'icon' => 'database',
        'submenu' => [[
            'name' => 'Таблицы',
            'icon' => 'folder',
            'link' => '/admin/statistics/database/tables'
        ], [
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/queries/history'
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/queries/charts'
        ],]
    ], [
        'name' => 'Кэш',
        'icon' => 'floppy',
        'submenu' => [[
            'name' => 'История',
            'icon' => 'clock',
            'link' => '/admin/statistics/cash/history',
        ], [
            'name' => 'Статистика',
            'icon' => 'chart-area',
            'link' => '/admin/statistics/cash/charts'
        ]]
    ]]
];

if ($adminRights->can('see-logs')) $globalItems[] = [
    'name' => 'Лог',
    'icon' => 'doc-text',
    'link' => '/admin/log'
];

$menu = ['items' => $globalItems];
?>

<div id="menu" data-props='<?= json_encode($menu, JSON_HEX_AMP) ?>'></div>