<?php /** @var frame\views\Layout $self */

use frame\views\Block;

$self->setLayout('admin-base');
?>

<table class="container">
    <tr>
        <td rowspan="2" class="sidebox"><?php (new Block('admin/left'))->show() ?></td>
        <td class="head-bar"><?php (new Block('admin/headbar'))->show() ?></td>
    </tr>
    <tr>
        <td class="content"><?php $self->showChild() ?></td>
    </tr>
</table>