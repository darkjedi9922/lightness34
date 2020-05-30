<?php /** @var frame\views\Page $self */

use engine\users\User;
use frame\auth\InitAccess;
use frame\tools\JsonEncoder;
use frame\tools\trackers\read\ReadLimitedProgressTracker as Tracker;
use engine\admin\LogsList;
use frame\lists\paged\PagerModel;
use frame\lists\paged\PagerView;

InitAccess::accessRight('admin', 'see-logs');

$me = User::getMe();
$logsList = new LogsList(PagerModel::getRoutePage());
$logger = $logsList->getLogger();
$logFile = $logger->getFile();
$logRecords = $logger->read();
$tracker = new Tracker('log', crc32($logFile), count($logRecords), $me->id);

$recordsProps = [];
foreach ($logRecords as $record) {
    $isCli = $record['ip'] === 'CLI';
    $recordsProps[] = [
        'type' => $record['type'],
        'ip' => !$isCli ? $record['ip'] : null,
        'cli' => $isCli,
        'time' => explode(' ', $record['date'])[1],
        'message' => $record['message']
    ];
}

$logPageProps = [
    'date' => str_replace('-', '.', basename($logFile, '.txt')),
    'records' => $recordsProps,
    'readedRecords' => $tracker->loadProgress(),
    'pagerHtml' => $logsList->countAll() > 1
        ? (new PagerView($logsList->getPager(), 'admin'))->getHtml()
        : null
];
$logPageProps = JsonEncoder::forHtmlAttribute($logPageProps);

$tracker->updateSetFinished();
?>

<div id="log-page" data-props="<?= $logPageProps ?>"></div>