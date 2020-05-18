<?php /** @var frame\views\Page $self */

use engine\users\cash\my_rights;
use engine\users\cash\user_me;
use frame\tools\Init;
use frame\tools\JsonEncoder;
use frame\tools\logging\SimpleLogger;
use frame\tools\trackers\read\ReadLimitedProgressTracker as Tracker;
use frame\stdlib\cash\config;

Init::accessRight('admin', 'see-logs');

$me = user_me::get();
$date = date('d-m-Y');
$logFile = config::get('core')->{'log.dir'} . "/$date.txt";
$logRecords = (new SimpleLogger($logFile))->read();
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
    'date' => date('d.m.Y'),
    'records' => $recordsProps,
    'readedRecords' => $tracker->loadProgress()
];
$logPageProps = JsonEncoder::forHtmlAttribute($logPageProps);

$tracker->updateSetFinished();
?>

<div id="log-page" data-props="<?= $logPageProps ?>"></div>