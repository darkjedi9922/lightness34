<?php /** @var frame\views\Page $self */

use engine\articles\actions\NewArticleAction;
use frame\actions\ViewAction;
use frame\tools\Init;

Init::accessRight('articles', 'add');

$action = new ViewAction(NewArticleAction::class);
?>

<div class="content">
    <?php if ($action->hasError(NewArticleAction::E_NO_TITLE)): ?><span class='error' style="margin-bottom:10px">Название не указано</span><?php endif ?>
    <?php if ($action->hasError(NewArticleAction::E_LONG_TITLE)): ?><span class='error' style="margin-bottom:10px">Название слишком большое</span><?php endif ?>
    <?php if ($action->hasError(NewArticleAction::E_NO_TEXT)): ?><span class='error' style="margin-bottom:10px">Текст статьи пуст</span><?php endif ?>
    <form class="form" id="article" action="<?=$action->getUrl()?>" method="post">
        <table cellspacing="10px" width="100%">
            <tr>
                <td class="form__label">
                    Название:<span class="form__require">*</span>
                </td>
                <td width="90%">
                    <input class="form__input" type="text" name="title" style="width:100%" value="<?= $action->getPost('title', '') ?>">
                </td>
            </tr>
            <tr>
                <td valign="baseline" class="form__label">
                    Текст:<span class="form__require">*</span>
                </td>
                <td width="90%">
                    <textarea class="form__input" form="article" name="text" style="width:100%" rows="6" spellcheck="false"><?= $action->getPost('text', '') ?></textarea>
                </td>
            </tr>
        </table>
        <button class="form__button">Создать</button>
    </form>
</div>