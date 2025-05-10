<?php
define('BILLINGMASTER', 1);

// Подключаем скрипт
require_once(dirname(__FILE__) . '/../../models/system.php');
require_once(dirname(__FILE__) . '/../../components/db.php');
require_once(dirname(__FILE__) . '/../../config/config.php');
require_once(dirname(__FILE__) . '/../../models/user.php');
require_once(dirname(__FILE__) . '/../../models/course.php');
$root = dirname(__FILE__) . '/../../';
define('ROOT', $root);
define("PREFICS", $prefics);

session_start();
$user_id = User::isAuth();
$answer_id = intval($_GET['id']);
$attach_name = $_GET['name'];

if (empty($answer_id) || empty($attach_name) || !$user_id) {
    header("Location: /");
}

$answer = Course::getAnswer($answer_id);
if ($answer && !empty($answer['attach'])) {
    $attachments = json_decode($answer['attach'], true);
    foreach ($attachments as $attachment) {
        if ($attach_name == $attachment['name']) {
            $path = $_SERVER['DOCUMENT_ROOT'] . urldecode($attachment['path']);
            if (file_exists($path)) {
                // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
                // если этого не сделать файл будет читаться в память полностью!
                if (ob_get_level()) {
                    ob_end_clean();
                }
                // заставляем браузер показать окно сохранения файла
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($attachment['name']));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($path));
                // читаем файл и отправляем его пользователю
                readfile($path);
                exit;
            } else {
                exit('Файл был перемещен или удален с сервера');
            }
        }
    }
} else {
    header("Location: /");
}