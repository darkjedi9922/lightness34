<?php /** @var frame\views\Page $self */

use engine\admin\actions\EmptyLogAction;
use frame\actions\ViewAction;
use engine\users\cash\my_rights;
use engine\users\cash\user_me;
use frame\tools\Init;
use frame\lists\base\FileLineList;
use frame\tools\files\File;
use frame\tools\JsonEncoder;
use frame\tools\Logger;
use frame\tools\trackers\read\FileReadTracker;

Init::accessRight('admin', 'see-logs');

if (!file_exists('log.txt')) File::create('log.txt');

$me = user_me::get();
$log = new FileLineList('log.txt');
$tracker = new FileReadTracker('log.txt', $me->id);
$lineCount = $tracker->countLines();
$unreadedLineCount = $tracker->countNewLines();
$readedLineCount = $lineCount - $unreadedLineCount;
$rights = my_rights::get('admin');

$action = new ViewAction(EmptyLogAction::class, ['file' => 'log.txt']);

$tracker->setReaded();

$recordsProps = [];
$records = (new Logger('log.txt'))->read();
foreach ($records as $record) {
    $isCli = $record['ip'] === 'CLI';
    $recordsProps[] = [
        'type' => $record['type'],
        'ip' => !$isCli ? $record['ip'] : null,
        'cli' => $isCli,
        'date' => $record['date'],
        'message' => $record['message']
    ];
}

$logPageProps = [
    'records' => $recordsProps,
    'clearLogUrl' => $action->getUrl()
];
$logPageProps = JsonEncoder::forHtmlAttribute($logPageProps);
?>

<div id="log-page" data-props="<?= $logPageProps ?>"></div>

<div class="box">
    <div style="float:left">
        <h3>
            Всего строк: <?= $lineCount ?><br>
            <span <?php if ($unreadedLineCount !== 0): ?>style="color:red"<?php endif ?>>Новых строк: <?= $unreadedLineCount ?></span>
        </h3>
    </div>
    <?php if ($lineCount !== 0 && $rights->can('clear-logs')): ?>
        <div style="text-align:right">
            <a href="<?= $action->getUrl() ?>" class="button">Очистить лог</a>
        </div>
    <?php endif?>
    <div style="clear:both"></div>
    <?php foreach ($log as $number => $line): 
    $isHeader = !empty($line) && $line[0] === '[';
    $isNew = $number > $readedLineCount;
    ?>
        <br><?= $number, ': ' ?>
        <span style="
            <?php if ($isHeader): ?>font-weight:bold<?php endif ?>
            <?php if ($isNew): ?>;color:red<?php endif ?>
        "><?= $line ?></span>
    <?php endforeach ?>
</div>