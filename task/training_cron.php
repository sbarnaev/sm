<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once (dirname(__FILE__) . '/../components/db.php');
require_once (dirname(__FILE__) . '/../config/config.php');

$root = dirname(__FILE__) . '/../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');

$time = time();
$name_jobs = "training_cron";

TrainingLesson::autoConfirmTask();
Training::eventsFinishProcess();
Training::userNotices();
Training::eventsChangeStatusHomeWork();

//Пишем в таблицу логов крона
//TODO в дальнейшем надо сделать модели и класс если этот функционал будет расширятся
$db = Db::getConnection();
$sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run) VALUES(:name_jobs, :last_run)  ON DUPLICATE KEY UPDATE last_run = :last_run";
$result = $db->prepare($sql);
$result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
$result->bindParam(':last_run', $time, PDO::PARAM_INT);
$result->execute();
//TODO В дальнейшем, все записи и отправки почты в
//кронзадачах обернуть в try catch и в случае не удачи
//писать ошибки в лог в таблицу cron_logs позже добавим там колонку error