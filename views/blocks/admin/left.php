<?php /** @var frame\views\Block $self */

use engine\users\cash\my_group;
use engine\users\cash\my_rights;

use function lightlib\versionify;

$group = my_group::get();
$rights = my_rights::get('admin');
?>

<ul class="menu" id="menu">
	<li><a href="/admin/home"><i class="fontello icon-home"></i> Главная</a></li>
	<li><a href="/admin/users"><i class="fontello icon-user"></i> Пользователи</a></li>
	<li><a href="/admin/articles"><i class="fontello icon-doc-text"></i> Статьи</a></li>
    <li><a><i class="fontello icon-rss"></i> Новое <i class="fontello icon-down-dir"></i></a>
        <ul>
            <li><a href="/admin/new/articles"><i class="fontello icon-doc-text"></i> Статьи</a></li>
        </ul>
    </li>
    <?php if($group->id === $group::ROOT_ID): ?>
        <li><a><i class="fontello icon-cog"></i> Настройки <i class="fontello icon-down-dir"></i></a>
            <ul>
                <li><a href="/admin/settings/core"><i class="fontello icon-sliders"></i> Общие</a></li>
                <li><a><i class="fontello icon-user"></i> Пользователи <i class="fontello icon-down-dir"></i></a>
                    <ul>
                        <li><a href="/admin/users/settings"><i class="fontello icon-cog"></i> Общие</a></li>
                        <li><a href="/admin/users/groups"><i class="fontello icon-id-card-o"></i> Группы</a></li>
                        <li><a href="/admin/users/genders"><i class="fontello icon-transgender"></i> Пол</a></li>
                        <li><a href="/admin/users/messages/settings"><i class="fontello icon-email"></i> Сообщения</a></li>
                    </ul>
                </li>
                <li><a href="/admin/articles/settings"><i class="fontello icon-doc-text"></i> Статьи</a></li>
                <li><a href="/admin/comments/settings"><i class="fontello icon-commenting"></i> Комментарии</a></li>
                <li><a href="/admin/settings/admin"><i class="fontello icon-television"></i> Админ-Панель</a></li>
            </ul>
        </li>
    <?php endif ?>
    <?php if ($rights->can('see-logs')): ?><li><a href="/admin/log"><i class="fontello icon-doc-text"></i> Лог</a></li><?php endif?>
</ul>
<script src="<?=versionify('public/scripts/admin-menu.js')?>"></script>