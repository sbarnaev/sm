<?php defined('BILLINGMASTER') or die;

class telegramController {

    /**
     * ПОЛУЧЕНИЕ ДАННЫХ ОТ TELEGRAM
     */
    public function actionGetUpdates() {
        $enable = Telegram::getStatus();
        
        if ($enable) {
            $settings = Telegram::getSettings();
            $params = unserialize($settings);
            
            $token = trim($params['params']['token']);
            if (!$token) {
                exit;
            }
            
            $api = new TelegramApi($token);
            $data = $api->getUpdatePost();
            
            if (!$data) {
                exit;
            } else {
                $message = $data['message'];
            }

            if (isset($message['new_chat_member']) && !empty($message['new_chat_member'])) { // добавляем пользователя в список участников и удаляем его из чатов, если у него не должно быть доступа к ним
                $chat_id = $message['chat']['id'];
                $user_id = (int)$message['new_chat_member']['id'];
                $user_name = $message['new_chat_member']['user_name'];
                $first_name = $message['new_chat_member']['first_name'];
                $last_name = $message['new_chat_member']['last_name'];

                $user = Telegram::getUserByUserId($user_id);
                $all_chats = Telegram::getChats();

                if ($user && $user['sm_user_id']) { // пользователь зарегистрирован в системе
                    $user_chats = Telegram::getChats($user['sm_user_id']);
                    if (!$user_chats || !in_array($chat_id, $user_chats)) { // удаляем пользователя из тг, если у него нет этого чата/канала в его группах/подписках
                        Telegram::delUserFromChats($user['sm_user_id'], $chat_id, false, Telegram::EVENT_DEL_USER_FROM_CHAT);
                    }
                } elseif($all_chats && in_array($chat_id, $all_chats) ) { // пользователя нет в списке участников или не зарегистрирован в системе
                    if (!$user) { // пользователь не зарегистрирован в системе
                        Telegram::addUnregisteredUser($user_id, $user_name, $first_name, $last_name);
                    }

                    if ($all_chats && in_array($chat_id, $all_chats)) { // удаляем пользователя из тг, если чат указан в настройках группы/подписки
                        Telegram::delUserFromChat($api, $user_id, $chat_id, Telegram::EVENT_DEL_USER_FROM_CHAT);
                    }
                }
            } else { // привязываем пользователя
                $user_id = Telegram::bindUser($api, $message);
                if ($user_id) { // удалить участника с нулевым sm_user_id, если есть
                    Telegram::delMemberByUserId($user_id, 0);
                }

            }
        }
    }


    /**
     * СОХРАНИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
     */
    public function actionSaveData() {
        if (isset($_POST['tg_link']) && isset($_POST['tg_username'])) {
            $resp = [
                'status' => false,
                'error_msg' => '',
                'hash' => '',
            ];

            $user_id = intval(User::checkLogged());
            if (!$user_id || empty($_POST['tg_link']) || empty($_POST['tg_username'])) {
                exit(json_encode($resp));
            }

            $settings = Telegram::getSettings();
            $params = unserialize($settings);

            $tg_username = substr(strip_tags($_POST['tg_username']), 0, 64);
            $count_users = User::getCountUsersByNickTelegram($tg_username);
            $user = $count_users == 1 ? User::getUserDataToNickTelegram($tg_username) : null;

            if (!$user || $user_id == $user['user_id']) { // если других пользователей с таким ником больше нет
                $res = User::updateNickTelegram($user_id, $tg_username);
                if ($res) {
                    $parts = parse_url($_POST['tg_link']);
                    parse_str($parts['query'], $query);
                    $hash = $query['start'];
                    if ($hash) {
                        $resp['status'] = Telegram::saveUser($user_id, $tg_username, $hash);
                        $resp['hash'] = $hash;
                    }
                }
            } else {
                $resp['status'] = true;
                $resp['error_msg'] = 'Пользователь с данным ником Telegram уже существует';
            }

            echo json_encode($resp);
        } else {
            $sys_settings = System::getSetting();
            require_once (ROOT . "/template/{$sys_settings['template']}/404.php");
        }
    }


    /**
     * ПРОВЕРИТЬ ПРИВЯЗКУ ПОЛЬЗОВАТЕЛЯ
     */
    public function actionCheckBindingUser() {
        if (isset($_POST['tg_username'])) {
            $resp = [
                'status' => false,
                'error_msg' => '',
                'bind' => false,
            ];

            $user_id = intval(User::checkLogged());
            if (!$user_id || empty($_POST['tg_username'])) {
                echo json_encode($resp);
                exit;
            }

            $resp['bind'] = Telegram::checkBindingUser($user_id, $_POST['tg_username']);
            $resp['status'] = true;

            echo json_encode($resp);
        } else {
            $sys_settings = System::getSetting();
            require_once (ROOT . "/template/{$sys_settings['template']}/404.php");
        }
    }
}