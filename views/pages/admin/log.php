<?php /** @var frame\views\Page $self */

use engine\users\User;
use frame\auth\InitAccess;
use frame\tools\JsonEncoder;
use engine\admin\logging\LogsList;
use frame\lists\paged\PagerModel;
use frame\lists\paged\PagerView;
use engine\admin\logging\LogReadTracker;

InitAccess::accessRight('admin', 'see-logs');

$me = User::getMe();
$logsList = new LogsList(PagerModel::getRoutePage());
$logger = $logsList->getLogger();
$logFile = $logger->getFile();
$logRecords = $logger->read();
$tracker = new LogReadTracker($me->id);

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
    'readedRecords' => $tracker->countReadedFromLog($logFile),
    'pagerHtml' => $logsList->countAll() > 1
        ? (new PagerView($logsList->getPager(), 'admin'))->getHtml()
        : null
];
$logPageProps = JsonEncoder::forHtmlAttribute($logPageProps);

$tracker->setLogReaded($logFile);
?>

<div id="log-page" data-props="<?= $logPageProps ?>"></div>