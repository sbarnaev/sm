<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once (dirname(__FILE__) . '/../components/db.php');
require_once (dirname(__FILE__) . '/../config/config.php');

$root = dirname(__FILE__) . '/../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');


$setting = System::getSetting();
$time = time();
$name_jobs = "system_cron";

if ($setting['logs_life_time']) {
    $delete_date = $time - $setting['logs_life_time'] * 86400;
    Email::delOldLogs($delete_date);
    SMS::delOldLogs($delete_date);
    Member::delOldLogs($delete_date);
    ActionLog::delOldLogs($delete_date);
    if (Telegram::getStatus()) {
        Telegram::delOldLogs($delete_date);
    }
}


$db = Db::getConnection();
$sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run) VALUES(:name_jobs, :last_run) ON DUPLICATE KEY UPDATE last_run = :last_run";
$result = $db->prepare($sql);
$result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
$result->bindParam(':last_run', $time, PDO::PARAM_INT);
$result->execute();