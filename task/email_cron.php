<?php define('BILLINGMASTER', 1);

define('START', microtime(1));

// Настройки системы
require_once (dirname(__FILE__) . '/../components/db.php');
require_once (dirname(__FILE__) . '/../config/config.php');

$root = dirname(__FILE__) . '/../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');
require_once (ROOT . '/vendor/autoload.php');


$setting = System::getSetting();
$sender_name = $setting['sender_name'];
$sender_email = $setting['sender_email'];

$time = time();
$name_jobs = "email_cron";
$error_send = false;

// Инициализировать объект Мейлера
if ($setting['smtp_ssl'] > 0) {
    $auth = $setting['smtp_ssl'] == 1 ? 'ssl' : 'tls';
    
    $transport = (new Swift_SmtpTransport($setting['smtp_host'], $setting['smtp_port'], $auth))
      ->setUsername($setting['smtp_user'])
      ->setPassword($setting['smtp_pass']);  
} else {
    $transport = (new Swift_SmtpTransport($setting['smtp_host'], $setting['smtp_port']))
      ->setUsername($setting['smtp_user'])
      ->setPassword($setting['smtp_pass']);   
}
$mailer = new Swift_Mailer($transport);

if (!empty($setting['smtp_private_key'])) {
    $signer = new Swift_Signers_DKIMSigner($setting['smtp_private_key'], $setting['smtp_domain'], $setting['smtp_selector']);
}
$db = Db::getConnection();

// Получить список заданий на отправку
$tasks = Responder::getTasksForAction();
						  

if ($tasks) {
    
    $task_ok = null;
    $task_fail = false;
    foreach($tasks as $task) { // Перебираем задания
        // Получить письмо по ID
        
        if ($task['letter']) {
            $letter = json_decode($task['letter'], true);
        } else {
            $result = $db->query('SELECT * FROM '.PREFICS.'email_letter WHERE letter_id = '.$task['letter_id'].' LIMIT 1 ');
            $letter = $result->fetch(PDO::FETCH_ASSOC);
        }

        
        if (isset($letter) && !empty($letter)) {
            $subject = $letter['subject'];
            
            // Получить данные юзера по емейл
            $email = $task['email'];
            $user = $task['user_name'];
            $promo = '';
			$promo_desc = '';
            
            // Получить промо коды по емейл
            if (strpos($letter['body'], '[PROMO]') !== false || strpos($letter['body'], '[PROMO_DESC]') !== false ) {
                $promo_data = System::getPromoByEmail($email, $time);

                if ($promo_data) {
                    $expire = date("d-m-Y H:i", $promo_data['finish']);
                    $promo = 'Ваш промо код: '.$promo_data['promo_code'].'<br /> Действителен до: '.$expire;
                    $promo_desc = 'Ваш промо код: '.$promo_data['promo_code'].'<br /> Действителен до: '.$expire.'<br />'.$promo_data['sale_desc'];
                }
            }
            
            if ($email) {
                $message_id = md5($time . '-' .$email); // id сообщения
                $key = md5($setting['secret_key'].$email); // ключ
                $did = $task['delivery_id'];
                
                // Реплейсим текст письма
                $unsub_link = $setting['script_url']. "/responder/unsubscribe/$key?did=$did&email=$email";
                $unsub_click = '<'.$setting['script_url']. "/responder/unsubclick/$key?did=$did&email=$email>";

                $replace = array(
                    '[NAME]' => $user,
					'[EMAIL]' => $email,
                    '[UNSUBSCRIBE]' => $unsub_link,
                    '[PROMO]' => $promo,
                    '[PROMO_DESC]' => $promo_desc,
					'[SUPPORT]' => $setting['support_email'],
                );

                $text = strtr($letter['body'], $replace);
				$subject = strtr($subject, $replace);					 
            
                
                // Письмо
                $message = new Swift_Message();
                if (!empty($setting['smtp_private_key'])) {
                    $message->attachSigner($signer);
                }
                $message->getHeaders()->addTextHeader('List-Unsubscribe', $unsub_click);
                
                // Message-ID
                /*
                $header_name = md5($email.$time);
                $chars = ['http://','https://']; // символы для удаления
                $domain = str_replace($chars, '', $setting['script_url']); // PHP код
                $message->getHeaders()->addIdHeader($header_name, $header_name.'@'.$domain);
                */
        
                $message->setFrom([$sender_email => $sender_name]);
                $message->setBody($text, 'text/html', 'utf-8');
                $message->addPart(strip_tags($text), 'text/plain');
                $message->setSubject($subject);
                $message->setTo($email); 

                try {
                    $send = $mailer->send($message);
                } catch (Exception $e) {
                    // Тут какая-то ошибка при отправке смотреть в файле error_email.txt
                    $error_send = true;
                    //$db = Db::getConnection();  
                    $sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run, jobs_error, text_error) VALUES(:name_jobs, :last_run, :jobs_error, :text_error)  ON DUPLICATE KEY UPDATE last_run = :last_run, text_error = :text_error";
                    $result = $db->prepare($sql);
                    $result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
                    $result->bindParam(':last_run', $time, PDO::PARAM_INT);
                    $result->bindParam(':jobs_error', $error_send, PDO::PARAM_INT);
                    $result->bindParam(':text_error', serialize($e), PDO::PARAM_STR);
                    try {
                        $result->execute();
                    } catch (Exception $e2) {
                        // Тут исключительный случай не смогли записать в БД
                        // по этому ошибку отправки письма пишем в файл error_email 
                        // а ошибку записи в БД пишем соотвествено в файл error_mysql
                        $filename = 'error_mysql.txt';
                        file_put_contents($filename, $e2);  
                        $filename = 'error_email.txt';
                        file_put_contents($filename, $e);
                    }
                    
                }

                if ($send) {
                    // Собираем ID тасков
                    $task_ok .= $task['task_id'].',';
                    //$del = Responder::DeleteTask($task['task_id']);
                    
							   
												
                    $action = 'send';
                    $descript = '';
																											 
					 
                    
                    //$send = Email::SendMessageToBlank('report@kasyanov.info', 'School-Master', 'Check-log', $task['letter_id'].' => '.$email.' => '.$ic);
                    Responder::WrtiteLog($task['letter_id'], $did, $time, $email, $action, $descript);   
                    
                    $log = Email::WriteLog($email, $text, $time, $subject, null);
                }
            } else {
                // Собираем ID неотправленных тасков
                $task_fail .= $task['task_id'].',';
                // тут скорее не ошибка, а то что юзера нет в выборке
                // Пишем ошибку в лог
                $action = 'error';
                $descript = "User with email : $email not found";
                Responder::WrtiteLog($task['letter_id'], $task['delivery_id'], $time, $email, $action, $descript);
                $del = Responder::DeleteTask($task['task_id']);
                continue;
            }
        } else {
												   
								
																																																								   
										 
																		 
																   
																		   
																			  
            continue;
        }
    }
    
    // УДАЛяем задачи и переотправляем неотправленные
    $del = Responder::DeleteTaskStr($task_ok);
    if($task_fail) $resend = Responder::ResendTask($task_fail);
}

//Пишем в таблицу логов крона
//TODO в дальнейшем надо сделать модели и класс если этот функционал будет расширятся
if (!$error_send) {
    //$db = Db::getConnection();  
    $sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run) VALUES(:name_jobs, :last_run)  ON DUPLICATE KEY UPDATE last_run = :last_run";
    $result = $db->prepare($sql);
    $result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
    $result->bindParam(':last_run', $time, PDO::PARAM_INT);
    $result->execute();
}
//TODO В дальнейшем, все записи и отправки почты в 
//кронзадачах обернуть в try catch и в случае не удачи 
//писать ошибки в лог в таблицу cron_logs позже добавим там колонку error
?>