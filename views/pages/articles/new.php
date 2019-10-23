<?php /** @var frame\views\Page $self */

use engine\articles\actions\NewArticleAction;
use frame\tools\Init;

Init::accessRight('articles', 'add');

$action = new NewArticleAction;
?>

<div class="content">
    <?php if ($action->hasError($action::E_NO_TITLE)): ?><span class='error'>Название не указано</span><br/><?php endif ?>
    <?php if ($action->hasError($action::E_LONG_TITLE)): ?><span class='error'>Название слишком большое</span><br/><?php endif ?>
    <?php if ($action->hasError($action::E_NO_TEXT)): ?><span class='error'>Текст статьи пуст</span><br/><?php endif ?>
    <form id="article" action="<?=$action->getUrl()?>" method="post">
        <table cellspacing="10px" width="100%">
            <tr>
                <td>
                    Название:<span class="require">*</span>
                </td>
                <td width="90%">
                    <input type="text" name="title" style="width:100%" value="<?= $action->getData('post', 'title', '') ?>">
                </td>
            </tr>
            <tr>
                <td valign="baseline">
                    Текст:<span class="require">*</span>
                </td>
                <td width="90%">
                    <textarea form="article" name="text" style="width:100%" rows="6" spellcheck="false"><?= $action->getData('post', 'text', '') ?></textarea>
                </td>
            </tr>
        </table>
        <button>Создать</button>
    </form>
</div>