<?php /** @var frame\views\Block $self */

use engine\users\cash\my_group;
use engine\users\cash\my_rights;

use function lightlib\versionify;

$group = my_group::get();
$rights = my_rights::get('admin');
?>

<ul class="menu" id="menu">
    <li class="menu__item">
        <a class="menu__link" href="/admin/home">
            <i class="menu__icon fontello icon-home"></i>
            <span class="menu__label">Главная</span>
        </a>
    </li>
    <li class="menu__item">
        <a class="menu__link" href="/admin/users">
            <i class="menu__icon fontello icon-user"></i>
            <span class="menu__label">Пользователи</span>
        </a>
    </li>
    <li class="menu__item">
        <a class="menu__link" href="/admin/articles">
            <i class="menu__icon fontello icon-doc-text"></i>
            <span class="menu__label">Статьи</span>
        </a>
    </li>
    <li class="menu__item">
        <span class="menu__link menu__link--parent">
            <i class="menu__icon fontello icon-rss"></i>
            <span class="menu__label">Новое</span>
            <i class="menu__arrow fontello icon-down-dir"></i>
        </span>
        <ul class="menu__submenu">
            <li class="menu__item">
                <a class="menu__link" href="/admin/new/articles">
                    <i class="menu__icon fontello icon-doc-text"></i>
                    <span class="menu__label">Статьи</span>
                </a>
            </li>
        </ul>
    </li>
    <?php if ($group->id === $group::ROOT_ID) : ?>
        <li class="menu__item">
            <a class="menu__link menu__link--parent">
                <i class="menu__icon fontello icon-cog"></i>
                <span class="menu__label">Настройки</span>
                <i class="menu__arrow fontello icon-down-dir"></i>
            </a>
            <ul class="menu__submenu">
                <li class="menu__item">
                    <a class="menu__link" href="/admin/settings/core">
                        <i class="menu__icon fontello icon-sliders"></i>
                        <span class="menu__label">Общие</span>
                    </a>
                </li>
                <li class="menu__item">
                    <a class="menu__link menu__link--parent">
                        <i class="menu__icon fontello icon-user"></i>
                        <span class="menu__label">Пользователи</span>
                        <i class="menu__arrow fontello icon-down-dir"></i>
                    </a>
                    <ul class="menu__submenu">
                        <li class="menu__item">
                            <a class="menu__link" href="/admin/settings/users">
                                <i class="menu__icon fontello icon-cog"></i>
                                <span class="menu__label">Общие</span>
                            </a>
                        </li>
                        <li class="menu__item">
                            <a class="menu__link" href="/admin/users/groups">
                                <i class="menu__icon fontello icon-id-card-o"></i>
                                <span class="menu__label">Группы</span>
                            </a>
                        </li>
                        <li class="menu__item">
                            <a class="menu__link" href="/admin/users/genders">
                                <i class="menu__icon fontello icon-transgender"></i>
                                <span class="menu__label">Пол</span>
                            </a>
                        </li>
                        <li class="menu__item">
                            <a class="menu__link" href="/admin/settings/messages">
                                <i class="menu__icon fontello icon-email"></i>
                                <span class="menu__label">Сообщения</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu__item">
                    <a class="menu__link" href="/admin/settings/articles">
                        <i class="menu__icon fontello icon-doc-text"></i>
                        <span class="menu__label">Статьи</span>
                    </a>
                </li>
                <li class="menu__item">
                    <a class="menu__link" href="/admin/settings/comments">
                        <i class="menu__icon fontello icon-commenting"></i>
                        <span class="menu__label">Комментарии</span>
                    </a>
                </li>
                <li class="menu__item">
                    <a class="menu__link" href="/admin/settings/admin">
                        <i class="menu__icon fontello icon-television"></i>
                        <span class="menu__label">Админ-Панель</span>
                    </a>
                </li>
            </ul>
        </li>
    <?php endif ?>
    <?php if ($rights->can('see-logs')) : ?>
        <li class="menu__item">
            <a class="menu__link menu__link--parent">
                <i class="menu__icon fontello icon-chart-bar"></i>
                <span class="menu__label">Статистика</span>
                <i class="menu__arrow fontello icon-down-dir"></i>
            </a>
            <ul class="menu__submenu">
                <li class="menu__item">
                    <a class="menu__link" href="/admin/statistics/routes">
                        <i class="menu__icon fontello icon-link"></i>
                        <span class="menu__label">Маршруты</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu__item">
            <a class="menu__link" href="/admin/log">
                <i class="menu__icon fontello icon-doc-text"></i>
                <span class="menu__label">Лог</span>
            </a>
        </li>
    <?php endif ?>
</ul>
<script src="<?= versionify('public/scripts/admin-menu.js') ?>"></script>