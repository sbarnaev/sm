<?php defined('BILLINGMASTER') or die;

class responderController {
    
    
    
    // ПОДПИСКА НА РАССЫЛКУ через html форму
    public function actionSubscribe($id)
    {
        $id = intval($id);
        if(isset($_POST['subscribe']) && !empty($_POST['email'])){
            
            $setting = System::getSetting();
            $date = time();
            $cookie = $setting['cookie'];
            $responder_setting = unserialize(Responder::getResponderSetting());
            $error = false;
            $delivery = Responder::getDeliveryData($id);
			if(!$delivery) exit();
            
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) !== false) {
                
                $email = htmlentities($_POST['email']);
                $time = time();
                $subs_key = md5($email . $time);
                $cancelled = 0;
                $spam = 0;
                $param = $date.';0;;/form';
                
                if(isset($_POST['name'])) $name = htmlentities($_POST['name']);
                else $name = $responder_setting['params']['name'];
                
                $phone = isset($_POST['phone']) ? htmlentities($_POST['phone']) : null;
                
                // Добавить в карту рассылок
                if($delivery['confirmation'] > 0) $confirmed = 0;
                else $confirmed = $time;
                
                // Создать запись в карте
                $add = Responder::addSubsToMap($id, $email, $name, $phone, $time, $subs_key, $confirmed, $cancelled, $spam, $param, $responder_setting, $setting);
                if(!$add) $error = 'Вы уже подписаны на данную рассылку';
                    
                  
            } else $error = 'Не корректный e-mail адрес';
            
            
            require_once (ROOT . '/template/'.$setting['template'].'/views/responder/confirm.php');
            
            
        } else header("Location: /");
    }
    
    
    
    // ПОДТВЕРЖДЕНИЕ ПОДПИСКИ ПОЛЬЗОВАТЕЛЯ
    public function actionConfirm($delivery_id)
    {
        $setting = System::getSetting();
        
        if (isset($_GET['email']) && isset($_GET['key'])) {
            $delivery_id = intval($delivery_id);
            $cookie = $setting['cookie'];
            $responder_setting = unserialize(Responder::getResponderSetting());
        
            // получить данные рассылки по id 
            $delivery = Responder::getDeliveryData($delivery_id);
            
            if ($delivery) {
                $email = htmlentities($_GET['email']);
                $key = htmlentities($_GET['key']);
                
                // получить запись в карте подписок и обновить статус confirmed с 0 на дату в unix 
                $row = Responder::getSubsMapRow($email, $delivery['delivery_id']);
                
                if ($row) {
                    if ($upd = Responder::updateSubsRow($row['id'], time())) {
                        require_once (ROOT . '/template/'.$setting['template'].'/views/responder/success-confirm.php');
                        
                        // Получить письма автосерии
                        $letter_list = Responder::getAutoLetterList($delivery_id);
                        
                        if ($letter_list) {
                            foreach ($letter_list as $letter) {
                                $send = time() + ($letter['send_time'] * 3600);
                                $status = 0;
                                $task = Responder::AddTask($delivery_id, $letter['letter_id'], $email, $send, $status);
                            }
                        }
                        
                        // Записать в юзеры (если ещё нет в базе)
                        $param = htmlentities($_COOKIE["$cookie"]);
                        $send_pass = $responder_setting['params']['send_pass'];
                        $time = time();
                        
                        if (!User::searchUser($row['email'])) {
                            $add_user = User::AddNewClient($row['subs_name'], $row['email'], $row['phone'], null, null,
                                null, 'user', 0, $time, 'subscription', $param, 1,
                                null, null, $send_pass, $setting['register_letter']
                            );
                        }
                        exit();
                    }
                } else {
                    echo '<p>Вы уже подтвердили ваш e-mail или произошла ошибка<p>';
                }
            }
        } else {
            header("Location: ".$setting['script_url']);
        }
    }
    
    
    
    // ОТПИСКА ОТ РАССЫЛКИ
    public function actionUnsubscribe($key)
    {
        if(isset($_GET['did']) && isset($_GET['email'])){
            $setting = System::getSetting();
            $did = intval($_GET['did']);
            $email = htmlentities($_GET['email']);
            $key = htmlentities($key);
            
            $row = Responder::getSubsMapRow($email, $did);
            if($key == md5($setting['secret_key'].$email)){
                
            
                if(isset($_POST['gone']) && isset($_POST['type'])){
                
                    $type = htmlentities($_POST['type']);
                    $reason = htmlentities($_POST['why']);
                    
                    switch($type){
                        
                        case 'single':
                        // Удалить одну подписку
                        $del = Responder::DeleteSubsRow($email, $did);
                        $none = 0;
                        break;
                        
                        case 'all':
                        //Удалить все подпсики
                        $del = Responder::DeleteSubsRow($email, 0);
                        $del_subs = Responder::DeleteIsSubs($email, 0);
                        $did = 0;
                        $none = 0;
                        break;
                        
                        case 'delete':
                        // удалить юзера по email
                        $user = User::getUserDataByEmail($email);
                        $del = User::deleteUser($user['user_id']);
                        $none = 0;
                        break;
                        
                        case 'none':
                        $none = 1;
                        break;
                        
                    }
                    
                    // Записать причину отписки в базу
                    $write = Responder::WriteUnsubReason($email, $did, $reason, $type);
                    if(isset($none) && $none == 1) {
                        require_once (ROOT . '/template/'.$setting['template'].'/views/responder/no-unsubscribe.php');
                        return true;   
                    }
                    
                    
                    if(isset($none) && $none == 0) {
                        require_once (ROOT . '/template/'.$setting['template'].'/views/responder/ok-unsubscribe.php');
                        return true;   
                    }
                
                }
            
            
            require_once (ROOT . '/template/'.$setting['template'].'/views/responder/unsubscribe.php');
               
                
            } else //header("Location: ".$setting['script_url']);
            exit('Not key valid');
            
        } else // header("Location: ".$setting['script_url']);
        exit('not param');
    }
    
    
    
    // МНГНОВЕННАЯ ОТПИСКА
    public static function actionUnsubclick($key)
    {
        if(isset($_GET['did']) && isset($_GET['email'])){
            $setting = System::getSetting();
            $did = intval($_GET['did']);
            $email = $_GET['email'];
            $key = htmlentities($key);
            
            $row = Responder::getSubsMapRow($email, $did);
            
            if($key == md5($setting['secret_key'].$email)){
                
                $del = Responder::DeleteSubsRow($email, $did);
                echo '<h1 style="text-align:center; padding:1em 0;">Всё хорошо.</h1><h2 style="text-align:center">Вы успешно отписаны от рассылки.</h2>';
                exit();
              
                
            } else exit('Not key valid');
            
        } else exit('not param');
        
        
    }
    
    
    
    
    
    
}