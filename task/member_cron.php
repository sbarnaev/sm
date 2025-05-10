<?php define('BILLINGMASTER', 1); 

// 1 раз в час - норм.

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
$name_jobs = "member_cron";

// Получить список планов подписки и перебрать их

$plane_list = Member::getPlanes(1);

if($plane_list){
	foreach($plane_list as $plane) {
		if ($plane['renewal_type'] != 3) {
			$product = Product::getProductById($plane['renewal_product']);
			if(!$product) continue;
			
			if ($plane['renewal_type'] == 1) {
				$link = $setting['script_url'].'/buy/'.$product['product_id'];
			} elseif ($plane['renewal_type'] == 2) {
				$link = $setting['script_url'].'/catalog/'.$product['product_alias'];
			}
		} else {
			$link = $plane['renewal_link'];
		}

		for ($x=1; $x<=3; $x++) {
			$letter_time_key = "letter_{$x}_time";
			$letter_text_key = "letter_{$x}";
			$letter_subj_key = "letter_{$x}_subj";

            $sms_status_key = "sms{$x}_status";
            $sms_text_key = "sms{$x}_text";

			if (isset($plane[$letter_time_key]) && $plane[$letter_time_key] != 0) {
				$kick_time = $now + $plane[$letter_time_key] * 3600;
				$search_list = Member::SearchExpiresForSendMess($plane['id'], $kick_time, $x - 1);

				if ($search_list) {
					foreach($search_list as $item) {
						// Получить данные юзера
						$user = User::getUserById($item['user_id']);

						// Отправить письмо клиенту
                        $send = Email::SendExpirationMessageByClient($user['email'], $user['user_name'],
                            $plane[$letter_subj_key], $plane[$letter_text_key],"$link?subs_id={$item['id']}"
                        );

                        if ($plane[$sms_status_key] && $user['phone']) {
                            SMS::sendNotice2ExpireSubs($user['user_name'], "$link?subs_id={$item['id']}",
                                $user['phone'], $plane[$sms_text_key]
                            );
                        }

						// Записать notif в карту
						$upd = Member::updateNotifFromMap($item['id'], $x);
						
						//$send = Email::SendMessageToBlank('report@kasyanov.info', '', 'ddd', $text);
					}
				}
			}
			
		}
	}
}

//Member::SendExpirationMessage();
$planes = Member::SearchExpirePlane();

if ($planes) {
    Member::deleteExpirePlanes($planes);
    Member::addUsersToGroupsByExpPlns($planes);
    Member::addPlanesToUser($planes);
}

//Пишем в таблицу логов крона
//TODO в дальнейшем надо сделать модели и класс если этот функционал будет расширятся
$db = Db::getConnection();  
$sql = "INSERT INTO ".PREFICS."cron_logs(jobs_cron, last_run) VALUES(:name_jobs, :last_run)  ON DUPLICATE KEY UPDATE last_run = :last_run";
$result = $db->prepare($sql);
$result->bindParam(':name_jobs', $name_jobs, PDO::PARAM_STR);
$result->bindParam(':last_run', $now, PDO::PARAM_INT);
$result->execute();

//Email::TestEmail('Отправка сообщений юзерам про окончание подпсики');

//TODO В дальнейшем, все записи и отправки почты в 
//кронзадачах обернуть в try catch и в случае не удачи 
//писать ошибки в лог в таблицу cron_logs позже добавим там колонку error
?>