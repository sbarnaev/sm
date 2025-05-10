<?php defined('BILLINGMASTER') or die;


class Conditions {
    
    // ОБРАБОТЧИК УСЛОВИЙ
    public static function renderCond($cond, $now)
    {
        $action = null;

        $db = Db::getConnection();
        // Послдений вход более XX часов
        if ($cond['type'] == 1) {
            $from = $now - $cond['value_xx'] * 3600;
            $to = $from + $cond['period'] * 60;
            
            $sql = "SELECT * FROM ".PREFICS."users WHERE status = 1 AND last_visit > $from AND last_visit < $to ORDER BY user_id DESC";
            $result = $db->query($sql);
    
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }
        
        // Последнее выполненное задание более XX часов
        if ($cond['type'] == 2) {
            $from = $now - $cond['value_xx'] * 3600;
            $to = $from + $cond['period'] * 60;
            
            $sql = "SELECT * FROM ".PREFICS."users WHERE status = 1 AND user_id IN ( SELECT user_id
                    FROM ".PREFICS."course_lesson_map WHERE date > $from AND date < $to ) ORDER BY user_id DESC";
            $result = $db->query($sql);
    
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }
        
        
        
        // До дня рождения XX дней
        if ($cond['type'] == 3) {
            $month = date("n", $now); // № месяца сегодня
            $day = date("j", $now); // дата сегодня
            $year = date("y", $now); // год 4 цифры
            
            // Дни в месяце (високос/невисокос)
            $m30 = array(4,6,9,11);
            $visikos = array(2020, 2024, 2028, 2032);
            $max_days = 31;
            
            if(in_array($month, $m30)){ // если текущий месяц есть в массиве, то max дней = 30
                $max_days = 30;
            } elseif($month == 2) { // если сейчас февраль
                if (in_array($year, $visikos)) {
                    $max_days = 29;
                } else {
                    $max_days = 28;
                }
            }
            
            $from = $day + $cond['value_xx']; // 10 + 2 = 12
            
            if ($from > $max_days) {
                $from = $from - $max_days;
                $month = $month + 1;
            }

            $sql = "SELECT user_id, user_name, email, phone, surname, nick_telegram, nick_instagram, from_id
                    FROM ".PREFICS."users WHERE bith_month = $month AND bith_day = $from AND status = 1 ORDER BY user_id DESC";
            $result = $db->query($sql);
    
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            if(!empty($data)){
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }
        
        
        // ПРИНАДЛЕЖИТ ГРУППЕ
        if ($cond['type'] == 4) {
            $group_id = intval($cond['value_xx']);
            
            $sql = "SELECT user_id, user_name, email, phone, surname, nick_telegram, nick_instagram, from_id
                    FROM ".PREFICS."users WHERE user_id IN ( SELECT user_id FROM ".PREFICS."user_groups_map
                    WHERE group_id = $group_id ) AND status = 1 ORDER BY user_id DESC";
            $result = $db->query($sql);
    
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }

        // Подписка мембершипа заканчивается через XX дней.
        if ($cond['type'] == 5) {

            $aboutday = intval($cond['value_xx']); // К текущему времени прибавляем ХХ дней
            
            
            $sql = "SELECT t1.user_id, t2.user_name, t2.email, t2.phone, t2.surname, t2.nick_telegram, t2.nick_instagram, t2.from_id
                    FROM ".PREFICS."member_maps as t1 LEFT JOIN ".PREFICS."users as t2 ON t1.user_id = t2.user_id
                    WHERE DATEDIFF(FROM_UNIXTIME(t1.end),now())=$aboutday AND t2.status = 1 ORDER BY t1.user_id DESC";

            $result = $db->query($sql);

            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }

        // НЕ ПРИНАДЛЕЖИТ НИКАКОЙ ГРУППЕ:
        if ($cond['type'] == 6) {
            
            $sql = "SELECT user_id, user_name, email, phone, surname, nick_telegram, nick_instagram, from_id
                    FROM ".PREFICS."users WHERE user_id NOT IN ( SELECT DISTINCT user_id FROM ".PREFICS."user_groups_map
                    ) AND status = 1 ORDER BY user_id DESC";
            $result = $db->query($sql);
    
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }
        
        
        // Тестовое, отправить по юзерам по ID
        if ($cond['type'] == 100) {
            $value = $cond['cond_desc'];
            $sql = "SELECT user_id, user_name, email, phone, surname, nick_telegram, nick_instagram, from_id
                    FROM ".PREFICS."users WHERE user_id IN ( $value) ORDER BY user_id DESC";
            
            $result = $db->query($sql);
    
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            if (!empty($data)) {
                $action = self::actionCond($data, $cond, $now);
            } else {
                return false;
            }
        }
        
        return $action;
    }


    // ДОБАВИТЬ ГРУППЫ
    private static function addGroups($cond, $data) {
        $add_groups = unserialize(base64_decode($cond['add_groups']));
        $acts = [];

        foreach($add_groups as $group){
            $acts[] = User::WriteUserGroup($data['user_id'], $group);
        }
        
        return empty($acts) || in_array(false, $acts) ? false : true;
    }

    
    // УДАЛИТЬ ГРУППЫ
    private static function delGroups($cond, $data) {
        $del_groups = unserialize(base64_decode($cond['del_groups']));

        return User::deleteUserGroupsFromList($data['user_id'], $del_groups);
    }
    
    
    // ОТПРАВИТЬ ПИСЬМО
    private static function sendLetter($cond, $data) {
        $replace = array(
            '[NAME]' => $data['user_name'],
            '[SURNAME]' => $data['surname'],
            '[EMAIL]' => $data['email'],
        );

        $text = strtr($cond['letter'], $replace);
        $act = Email::SendMessageToBlank($data['email'], $data['user_name'], $cond['subject'], $text);

        return $act;
    }


    // ОТПРАВИТЬ SMS
    private static function sendSMS($message, $data) {
        $replace = array(
            '[NAME]' => $data['user_name'],
            '[SURNAME]' => $data['surname'],
            '[EMAIL]' => $data['email'],
            '[PHONE]' => $data['phone'],
        );

        $text = strtr($message, $replace);
        $act = SMSC::sendSMS($data['phone'], $text);

        return $act;
    }


    // ДОБАВИТЬ ПОДПИСКУ
    private static function addSubscribe($data, $time, $delivery_id, $confirmed, $responder_setting, $setting) {

        $subs_key = md5($data['email'] . $time);
        $param = $time.';0;;/condition';

        $act = Responder::addSubsToMap($delivery_id, $data['email'], $data['user_name'], $data['phone'], $time, $subs_key,
            $confirmed, 0, 0, $param, $responder_setting, $setting
        );
        
        return $act;
    }

    // УДАЛИТЬ ПОДПИСКУ
    private static function delSubscribe($unsubscribe, $email) {
        $acts = [];

        foreach($unsubscribe as $delivery_id){
            // удалить из карты подписок
            $acts[] = Responder::DeleteSubsRow($email, $delivery_id);
            // удалить письма рассылки
            $acts[] = Responder::DeleteTaskByEmail($email, $delivery_id);
        }
    
        return empty($acts) || in_array(false, $acts) ? false : true;
    }


    // ИСПОЛНЕНИЕ УСЛОВИЙ
    public static function actionCond($data_arr, $cond, $time)
    {
        $acts = [];
        
        if ($cond['delivery_id'] != 0) {
            $delivery = Responder::getDeliveryData($cond['delivery_id']);
            $confirmed = $delivery['confirmation'] > 0 ? 0 : $time;
            $responder_setting = unserialize(Responder::getResponderSetting());
            $setting = System::getSetting();
        }
        
        $unsubscribe = $cond['unsubscribe'] ? unserialize(base64_decode($cond['unsubscribe'])) : null;
        
        foreach($data_arr as $data) {
            if ($cond['del_groups'] != null) { // Удаление групп
                $acts[] = self::delGroups($cond, $data);
            }
    
            if ($cond['add_groups'] != null) { // Добавление групп
                $acts[] = self::addGroups($cond, $data);
            }
            
            if ($cond['send_letter'] == 1) { // Отправка письма
                $acts[] = self::sendLetter($cond, $data);
            }
            
            if ($cond['send_sms'] == 1 && !empty($data['phone'])) { // Отправка sms
                $acts[] = self::sendSMS($cond['message'], $data);
            }

            if ($cond['delivery_id'] != 0) { // Подписка на рассылку
                $acts[] = self::addSubscribe($data, $time, $cond['delivery_id'], $confirmed, $responder_setting, $setting);
            }

            if ($unsubscribe) { // Отписка от рассылки
                $acts[] = self::delSubscribe($unsubscribe, $data['email']);
            }
        }
        
        return empty($acts) || in_array(false, $acts) ? false : true;
    }
    
    
    // ПОИСК УСЛОВИЙ
    public static function searchConditions($time)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."conditions WHERE status = 1 AND use_cron = 1 AND next_action < $time ORDER BY id DESC");
        
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }
    
    
    
    
    // ОБНОВИТЬ УСЛОВИЕ ПОСЛЕ ОБРАБОТКИ
    public static function updateCond($cond_id, $period, $action)
    {
        $db = Db::getConnection();
        
        $sql = 'UPDATE '.PREFICS.'conditions SET next_action = :next_action WHERE id = '.$cond_id;
        $result = $db->prepare($sql);
        
        $next_action = $action + $period * 60;
        $result->bindParam(':next_action', $next_action, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    // СПИСОК УСЛОВИЙ
    public static function getConditionsList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."conditions ORDER BY id DESC");
    
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    
        return !empty($data) ? $data : false;
    }
    
    
    
    // ДАННЫЕ УСЛОВИЯ ПО ID
    public static function getConditionData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."conditions WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }
    

    
    // ДАННЫЕ УСЛОВИЯ ПО CREATE_DATE
    public static function getCondByCreateDate($create_date)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."conditions WHERE create_date = $create_date LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }
    
    
    
    
    // ДОБАВИТЬ НОВОЕ УСЛОВИЕ
    public static function addNewCondition($name, $type, $value_xx, $status, $desc, $use_cron, $period, $sql_data,
                                           $add_groups, $del_groups, $delivery_id, $delivery_unsub, $send_letter, $subject, $letter,
                                           $send_sms, $message, $create_date)
    {
        $next_action = $create_date;
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'conditions (name, type, value_xx, status, cond_desc, sql_data, use_cron, period, add_groups, del_groups,
                        delivery_id, unsubscribe, send_letter, subject, letter, send_sms, message, create_date, next_action)
                VALUES (:name, :type, :value_xx, :status, :cond_desc, :sql_data, :use_cron, :period, :add_groups, :del_groups,
                        :delivery_id, :unsubscribe, :send_letter, :subject, :letter, :send_sms, :message, :create_date, :next_action)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        $result->bindParam(':value_xx', $value_xx, PDO::PARAM_INT);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':cond_desc', $desc, PDO::PARAM_STR);
        
        $result->bindParam(':sql_data', $sql_data, PDO::PARAM_STR);
        $result->bindParam(':add_groups', $add_groups, PDO::PARAM_STR);
        $result->bindParam(':del_groups', $del_groups, PDO::PARAM_STR);
    
        $result->bindParam(':delivery_id', $delivery_id, PDO::PARAM_INT);
        $result->bindParam(':unsubscribe', $delivery_unsub, PDO::PARAM_STR);
        
        $result->bindParam(':use_cron', $use_cron, PDO::PARAM_INT);
        $result->bindParam(':period', $period, PDO::PARAM_INT);
        
        $result->bindParam(':send_letter', $send_letter, PDO::PARAM_INT);
        $result->bindParam(':subject', $subject, PDO::PARAM_STR);
        $result->bindParam(':letter', $letter, PDO::PARAM_STR);
        $result->bindParam(':send_sms', $send_sms, PDO::PARAM_INT);
        $result->bindParam(':message', $message, PDO::PARAM_STR);
        
        $result->bindParam(':create_date', $create_date, PDO::PARAM_INT);
        $result->bindParam(':next_action', $next_action, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    
    // ИЗМЕНИТЬ УСЛОВИЕ
    public static function editCondition($id, $name, $type, $value_xx, $status, $desc, $use_cron, $period, $sql,
                                         $add_groups, $del_groups, $delivery_id, $delivery_unsub, $send_letter, $subject, $letter,
                                         $send_sms, $message)
    {
        
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'conditions SET name = :name, type = :type, value_xx = :value_xx, status = :status,
                cond_desc = :cond_desc, sql_data = :sql_data, use_cron = :use_cron, period = :period, add_groups = :add_groups,
                del_groups = :del_groups, send_letter = :send_letter, subject = :subject, letter = :letter,
                send_sms = :send_sms, message = :message, delivery_id = :delivery_id, unsubscribe = :unsubscribe WHERE id = '.$id;
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        $result->bindParam(':value_xx', $value_xx, PDO::PARAM_INT);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':cond_desc', $desc, PDO::PARAM_STR);
        
        $result->bindParam(':sql_data', $sql_data, PDO::PARAM_STR);
        $result->bindParam(':add_groups', $add_groups, PDO::PARAM_STR);
        $result->bindParam(':del_groups', $del_groups, PDO::PARAM_STR);
    
        $result->bindParam(':delivery_id', $delivery_id, PDO::PARAM_INT);
        $result->bindParam(':unsubscribe', $delivery_unsub, PDO::PARAM_STR);
        
        $result->bindParam(':use_cron', $use_cron, PDO::PARAM_INT);
        $result->bindParam(':period', $period, PDO::PARAM_INT);
        
        $result->bindParam(':send_letter', $send_letter, PDO::PARAM_INT);
        $result->bindParam(':subject', $subject, PDO::PARAM_STR);
        $result->bindParam(':letter', $letter, PDO::PARAM_STR);
        $result->bindParam(':send_sms', $send_sms, PDO::PARAM_INT);
        $result->bindParam(':message', $message, PDO::PARAM_STR);
        
        return $result->execute();
    }
    
    
    // УДАЛИТЬ УСЛОВИЕ
    public static function delCondition($id)
    {
        $db = Db::getConnection();
        
        $sql = 'DELETE FROM '.PREFICS.'conditions WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $result->execute();
    }
}