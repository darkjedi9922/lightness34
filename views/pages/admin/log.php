<?php /** @var frame\views\Page $self */

use engine\admin\actions\EmptyLogAction;
use frame\actions\ViewAction;
use engine\users\cash\my_rights;
use engine\users\cash\user_me;
use frame\tools\Init;
use frame\tools\JsonEncoder;
use frame\tools\Logger;
use frame\tools\trackers\read\ReadLimitedProgressTracker as Tracker;

Init::accessRight('admin', 'see-logs');

$me = user_me::get();
$rights = my_rights::get('admin');
$clear = new ViewAction(EmptyLogAction::class, ['file' => 'log.txt']);
$logRecords = (new Logger('log.txt'))->read();
$tracker = new Tracker('log', crc32('log.txt'), count($logRecords), $me->id);

$recordsProps = [];
foreach ($logRecords as $record) {
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
    'readedRecords' => $tracker->loadProgress(),
    'clearLogUrl' => $rights->can('clear-logs') ? $clear->getUrl() : null
];
$logPageProps = JsonEncoder::forHtmlAttribute($logPageProps);

$tracker->updateSetFinished();
?>

<div id="log-page" data-props="<?= $logPageProps ?>"></div>