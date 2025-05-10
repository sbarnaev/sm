<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once (dirname(__FILE__) . '/../components/db.php');
require_once (dirname(__FILE__) . '/../config/config.php');

$root = dirname(__FILE__) . '/../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');
require_once (ROOT . '/vendor/autoload.php');

$setting = System::getSetting();
$now = time();
$name_jobs = "installment_cron";

// получить список рассрочек
$installment_list = Product::getInstalments();

if ($installment_list) {
    // Перебрать их в цикле
    foreach($installment_list as $installment) {
        if ($installment['notif'] != null) {
            $notif = unserialize(base64_decode($installment['notif']));
            $count_notif = 0;
            
            for ($x=1; $x<=3; $x++) {
                $send_email = 'send_'.$x.'_email';
                $send_sms = 'send_'.$x.'_sms';
                $send_time = 'send_'.$x.'_time';
                $send_subject = 'send_'.$x.'_subject';
                $send_text = 'send_'.$x.'_text';
                $sms_text = 'send_'.$x.'_smstext';
            
                // напоминания
                if ($notif["$send_email"] == 1 || $notif["$send_sms"] == 1) { // Если указано отправлять емейл или sms
                    $count_notif++;
                    $kick = $now + $notif["$send_time"] * 3600; // Время для отправки сообщения
                    $status = 1; // активные рассрочки
                    $search_list = Order::searchInstallFromMap($installment['id'],$kick, $status, $x - 1);
                    
                    if ($search_list) {
                        foreach($search_list as $map_item) {
                            if ($map_item['notif'] == 0) { // Создаём заказ для рассрочки
                                $pay_actions = unserialize(base64_decode($map_item['pay_actions']));
                                $sum_to_pay = Installment::getSum2Pay($pay_actions, $map_item);
                                $new_order = Order::createNewOrderFromInstallment($map_item['order_id'], $sum_to_pay, $map_item['email'], $now, $map_item['id'], $map_item['installment_id']);
                                if ($new_order) {
                                    $comment = "Очередной платёж по рассрочке с ID ".$map_item['id'];
                                    $upd = Order::updateAdminCommentByOrder($new_order, $comment, $map_item['id']);
                                }
                            }
                            
                            // Получить данные юзера по емейл
                            $user = User::getUserDataByEmail($map_item['email']);
                            $order_data = Order::getOrderDataByID($map_item['order_id'], 100);
                            
                            $replace = [
                                '[CLIENT_NAME]' => $order_data['client_name'],
                                '[EMAIL]' => $order_data['client_email'],
                                '[ORDER]' => $order_data['order_date'],
                                '[LINK]' => $setting['script_url'].'/pay/'.$order_data['order_date'],
                            ];
                            
                            // Обновляем данные записи в карте рассрочек
                            $order_date = $x > 1 ? null : $now;
                            $upd_notif = Order::updateNotifCount($map_item['id'], $x, $order_date);
                            
                            if ($notif["$send_email"] == 1) {
                                // Формируем текст письма
                                $subject = $notif["$send_subject"];
                                $letter = $notif["$send_text"];
                                if ($x == 1) { // если первое напоминание, то подставляем дату
                                    $link = $setting['script_url'].'/pay/'.$now;
                                } else { // если не первое напоминание подставляем order_date из карты рассрочек
                                    $link = $setting['script_url'].'/pay/'.$map_item['next_order'];
                                }
                                
                                // Отправляем письмо
                                $send = Email::SendClientNotifAboutInstallment($map_item['email'], $user['user_name'], $now, $subject, $letter, $link);
                            }
                            
                            
                            if ($notif["$send_sms"] == 1) {
                                $message = $notif["$sms_text"];
                                $message = strtr($message, $replace);
                                if(!empty($user['phone'])) $send_sms = SMSC::sendSMS($user['phone'], $message);
                            }
                            
                            $now++; // добавляем +1 на всякий случай.
                        }
                    }
                }
            }
        
        
            // ПИСЬМА ПОСЛЕ ПРОСРОЧКИ
            $x = 1;
            for ($after_notif = 0; $after_notif<2; $after_notif++) {
    
                $after_time = 'time_'.$x.'_after';
                $after_subj = 'subject_'.$x.'_after';
                $after_text = 'text_'.$x.'_after';
                
                if (isset($notif["$after_time"]) && !empty($notif["$after_time"])) {
                    $kick = time() - $notif["$after_time"] * 3600;
                    $status = 1; // просроченные рассрочки
                    $expired = 1;
                    $search_list = Order::searchInstallFromMap($installment['id'],$kick, $status, $after_notif, $expired);
                    
                    if ($search_list) {
                        foreach($search_list as $map_item) {
                            // Получить данные юзера по емейл
                            $user = User::getUserDataByEmail($map_item['email']);
                            
                            // Формируем текст письма
                            $subject = $notif["$after_subj"];
                            $letter = $notif["$after_text"];
                            $link = $setting['script_url'].'/pay/'.$map_item['next_order']; // если не первое напоминание подставляем order_date из карты рассрочек
                            
                            // Отправляем письмо
                            $send = Email::SendClientNotifAboutInstallment($map_item['email'], $user['user_name'], $now, $subject, $letter, $link);
                            
                            $upd_notif = Order::updateNotifCount($map_item['id'], $after_notif + 1, null, $expired);
                        }
                        
                        //$send = Email::SendMessageToBlank('report@kasyanov.info', '', 'fff', 'время '. date("d.m.Y H:i:s", $kick) .' = notif  = '.$after_notif);
                    }
                    $x++;
                }
            }
        
        
            // ПОИСК ПРОСРОЧЕННЫХ РАССРОЧЕК
            
            $expired = $now - $installment['expired'] * 86400;
            $exp_list = Order::searchExpireInstallments($expired); // получаем карты истёкших рассрочек

            if ($exp_list) {
                foreach($exp_list as $exp_item) {
                    $order = [];
                    $order['order_id'] = $exp_item['order_id'];
                    $order['client_email'] = $exp_item['email'];
                    $order['installment_map_id'] = $exp_item['id'];
                    
                    $installment_data = Product::getInstallmentData($exp_item['installment_id']); // получаем настройки рассрочки
                    $type = 0;
                    $status = 9;
                    
                    // перебираем просроченные рассрочки и начинаем процедуру просрочки
                    $cancel = Order::endInstallment($order, $exp_item, $type);
                    if ($cancel) {
                        $stop = Order::updateInstallMentStatus($exp_item['id'], $status);
                    }
    
                    // РАСШИРЕНИЕ AmoCRM
                    if (System::CheckExtensension('amocrm', 1)) {
                        $order = Order::getOrder($order['order_id']);
                        if ($order) {
                            AmoCRM::updLead(AmoCRM::EVENT_TYPE_DEBTORS_INSTLMNT, $order);
                        }
                    }
                }
            }
        }
    }

}

//Пишем в таблицу логов крона
//TODO в дальнейшем надо сделать модели и класс если этот функционал будет расширятся
$jobs_error = null;
$db = Db::getConnection();  
//$sql = "INSERT ".PREFICS."cron_logs SET jobs_cron = :name_jobs, last_run = :last_run";
$sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run) VALUES(:name_jobs, :last_run)  ON DUPLICATE KEY UPDATE last_run = :last_run";
$result = $db->prepare($sql);
$result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
$result->bindParam(':last_run', $now, PDO::PARAM_INT);
$result->execute();
//TODO В дальнейшем, все записи и отправки почты в 
//кронзадачах обернуть в try catch и в случае не удачи 
//писать ошибки в лог в таблицу cron_logs позже добавим там колонку error
?>