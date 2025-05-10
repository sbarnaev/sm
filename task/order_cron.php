<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once (dirname(__FILE__) . '/../components/db.php');
require_once (dirname(__FILE__) . '/../config/config.php');

$root = dirname(__FILE__) . '/../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');
require_once (ROOT . '/vendor/autoload.php');

$time = time();
$name_jobs = "order_cron";

$setting = System::getSetting();
Reminder::remindClientLetter($setting);

// модуль оплаты post-credit
$pos_credit_data = Order::getPaymentSetting('poscredit');
if ($pos_credit_data['status']) {
    require_once (ROOT . '/payments/poscredit/result.php');
}

OrderTask::taskProcessing();

$db = Db::getConnection();  
$sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run) VALUES(:name_jobs, :last_run)  ON DUPLICATE KEY UPDATE last_run = :last_run";
$result = $db->prepare($sql);
$result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
$result->bindParam(':last_run', $time, PDO::PARAM_INT);
$result->execute();
//TODO В дальнейшем, все записи и отправки почты в 
//кронзадачах обернуть в try catch и в случае не удачи 
//писать ошибки в лог в таблицу cron_logs позже добавим там колонку error
?>