<?php defined('BILLINGMASTER') or die; 

class Member {

    /**
     * @param array $exceptions
     * @return array
     */
    public static function getFields($exceptions = []) {
        $fields = [
            'integer' => [
                'id', 'lifetime', 'max_periods', 'create_new', 'prolong_active', 'recurrent_enable',
                'amount', 'delay', 'access_id', 'renewal_type', 'renewal_product', 'status', 'letter_1_time',
                'letter_2_time', 'letter_3_time', 'sms1_status', 'sms2_status', 'sms3_status',
            ],
            'string' => [
                'name', 'service_name', 'related_planes', 'subs_desc', 'prolong_link', 'recurrent_label',
                'select_payments', 'manager_letter', 'period_type', 'letter_1', 'letter_1_subj', 'letter_2',
                'letter_2_subj', 'letter_3', 'letter_3_subj', 'renewal_link', 'del_groups', 'add_groups',
                'add_planes', 'del_tg_chats', 'sms1_text', 'sms2_text', 'sms3_text',
            ],
        ];

        return [
            'integer' => array_diff($fields['integer'], $exceptions),
            'string' => array_diff($fields['string'], $exceptions),
        ];
    }


    /**
     * @param $fields
     * @param $data
     * @return array
     */
    public static function beforeSave($fields, $data) {
        $manager_letter = [
            'subj_manager' => !empty($data['subj_manager']) ? htmlentities($data['subj_manager']) : null,
            'email_manager' => !empty($data['email_manager']) ? htmlentities($data['email_manager']) : null,
            'letter_manager' => !empty($data['letter_manager']) ? $data['letter_manager'] : null,
        ];
        $manager_letter = array_filter($manager_letter, 'strlen') ? base64_encode(serialize($manager_letter)) : null;

        $self_data = [
            'del_groups' => !empty($data['del_groups']) ? serialize($data['del_groups']) : null,
            'add_groups' => !empty($data['add_groups']) ? implode(',', $data['add_groups']) : null,
            'add_planes' => !empty($data['add_planes']) ? implode(',', $data['add_planes']) : null,
            'select_payments' => !empty($data['select_payments']) ? base64_encode(serialize($data['select_payments'])) : null,
            'manager_letter' => $manager_letter,
            'name' => htmlentities($data['name']),
            'service_name' => htmlentities($data['service_name']),
            'prolong_link' => htmlentities($data['prolong_link']),
            'related_planes' => !empty($data['related_planes']) ? implode(",", $data['related_planes']) : null,
            'del_tg_chats' => isset($data['del_tg_chats']) ? str_replace(' ', '', $data['del_tg_chats']) : null,
            'subs_desc' => htmlentities($data['subs_desc']),
            'renewal_link' => htmlentities($data['renewal_link']),
            'letter_1_subj' => htmlentities($data['letter_1_subj']),
            'letter_2_subj' => htmlentities($data['letter_2_subj']),
            'letter_3_subj' => htmlentities($data['letter_3_subj']),
            'letter_1' => trim($data['letter_1']),
            'letter_2' => trim($data['letter_2']),
            'letter_3' => trim($data['letter_3']),
            'recurrent_label' => htmlentities($data['recurrent_label']),
            'del_tg_chats' => isset($data['del_tg_chats']) ? str_replace(' ', '', $data['del_tg_chats']) : null,
            'name' => $data['name'],
            'period_type' => $data['period_type'],
            'sms1_text' => trim($data['sms1_text']),
            'sms2_text' => trim($data['sms2_text']),
            'sms3_text' => trim($data['sms3_text']),
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $fields['integer'])) {
                $self_data[$key] = (int)$value;
            }
        }

        return $self_data;
    }


    // Для теста 
    public static function getInvalidDate()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT id, end, subs_id, user_id FROM ".PREFICS."member_maps WHERE end > 1617278400 AND status = 1 AND subs_id = 8");
        
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    
        return !empty($data) ? $data : false;
    }
    
    
    // ОБНОВИТЬ КОЛ_ВО ОТПРАВЛЕННЫХ УВЕДОМЛЕНИЙ
    public static function updateNotifFromMap($id, $x)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'member_maps SET send_notification = :send_notification WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':send_notification', $x, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    // ПОИСК ПОДХОДЯЩИХ ПО СРОКУ ПОДПИСОК
    public static function SearchExpiresForSendMess($plane_id, $kick_time, $notif)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."member_maps WHERE subs_id = $plane_id AND end < $kick_time AND status = 1 AND send_notification = $notif ");
        
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    
        return !empty($data) ? $data : false;
        
    }
    
    
    public static function updateInvalidDate($item_id, $time)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'member_maps SET end = :time WHERE id = '.$item_id;
        $result = $db->prepare($sql);
        $result->bindParam(':time', $time, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    // ПОИСК ИСТЁКШИХ ПЛАНОВ
    public static function searchExpirePlane()
    {
        $db = Db::getConnection();
        $params = unserialize(self::getMembershipSetting());
        
        // определяем время когда у юзера есть время продлить истёкушю подписку
        $expire = !empty($params['params']['expires']) ? $params['params']['expires'] * 86400 : 0;
        $time = time() - $expire;
    
        // ищем подписки с учётом времени для продления (если в настройках указано 3 дня, то ищем подписки которые истекли 3 дня назад)
        $result = $db->query("SELECT id, subs_id, user_id FROM " . PREFICS . "member_maps WHERE end < $time AND status = 1 AND successed = 0");
    
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    
        return !empty($data) ? $data : false;
    }
    
    // УДАЛЕНИЕ ИСТЁКШИХ ПЛАНОВ
    public static function deleteExpirePlanes($planes)
    {
        $db = Db::getConnection();

        foreach($planes as $item) {
            $plane = self::getPlaneByID($item['subs_id']); // Получить данные плана подписки по id
            
            if ($plane['manager_letter'] != null) {
                $manager_letter = unserialize(base64_decode($plane['manager_letter']));
                
                if (isset($manager_letter['email_manager']) && !empty($manager_letter['email_manager'])) {
                    $subj_manager = isset($manager_letter['subj_manager']) ? $manager_letter['subj_manager'] : null;
                    $letter_manager = isset($manager_letter['letter_manager']) ? $manager_letter['letter_manager']  : null;
                    $send_custom = Email::sendLetterAboutExpireSubscription($manager_letter['email_manager'], $subj_manager, $letter_manager, $item['user_id']);
                }
            }

            if (!empty($plane['del_groups'])) { // Перебрать id групп которые нужно удалить при окончании подписки
                $groups = unserialize($plane['del_groups']);

                if ($groups) {
                    foreach ($groups as $group_id) {
                        $group_id = (int)$group_id;

                        if (!self::checkActiveSubsWithDelGroup($group_id, $item['user_id'])) { // если этой группы нет у действующей подписки пользователя
                            User::deleteUserGroup($item['user_id'], $group_id); // удалить пользователя из user_groups_map
                        }
                    }
                }
            }

            // Удаление пользователя из telegram
            if (Telegram::getStatus() && $plane['del_tg_chats']) {
                Telegram::delUserFromChatsToSub($item['user_id'], $plane['del_tg_chats'], $item['subs_id']);
            }
            
            // Изменить статус подписки на 0
            $sql = 'UPDATE '.PREFICS.'member_maps SET status = 0 WHERE id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $item['id'], PDO::PARAM_INT);
            
            return $result->execute();
        }
    }
    
    // ДОБАВЛЕНИЕ ПОЛЬЗОВАТЕЛЕЙ В ГРУППЫ ПРИ ИСТЕЧЕНИИ ПЛАНОВ
    public static function addUsersToGroupsByExpPlns($planes)
    {
        $db = Db::getConnection();
        
        foreach($planes as $item) {
            $plane = self::getPlaneByID($item['subs_id']); // Получить данные плана подписки по id
            
            // Перебрать id групп которые нужно удалить при окончании подписки
            if (!empty($plane['add_groups'])) {
                $groups = explode(',', $plane['add_groups']);
                $date = time();
                
                foreach ($groups as $group) {
                    $sql = 'INSERT INTO '.PREFICS."user_groups_map (group_id, user_id, date) VALUES (:group_id, :user_id, $date)";
                    
                    $result = $db->prepare($sql);
                    $result->bindParam(':group_id', $group, PDO::PARAM_INT);
                    $result->bindParam(':user_id', $item['user_id'], PDO::PARAM_INT);
                    $result->execute();
                }
            }
        }
    }

    /**
     * ДОБАВЛЕНИЕ ПЛАНОВ ПОДПИСОК ПОЛЬЗОВАТЕЛЯМ ПО ОКОНЧАНИИ ПЛАНА ПОДПИСОК
     * @param $planes
     */
    public static function addPlanesToUser($planes)
    {
        foreach($planes as $item) {
            $plane = self::getPlaneByID($item['subs_id']); // Получить данные плана подписки по id
            
            // Перебрать id планов подписок которые нужно добавить при окончании подписки
            if (!empty($plane['add_planes'])) {
                $add_planes = explode(',', $plane['add_planes']);
                
                foreach ($add_planes as $add_plane) {
                    Member::addMember($add_plane, $item['user_id'], 1, null, 0);
                }
            }
        }
    }
    
    
    // СПИСОК ПЛАНОВ
    public static function getPlanes($status = null)
    {
        $db = Db::getConnection();

        $query = "SELECT * FROM ".PREFICS."member_planes";
        $query .= ($status != null ? " WHERE status = $status" : '') . ' ORDER BY id DESC';

        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК АКТИВНЫХ ПЛАНОВ ДЛЯ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @return array|bool
     */
    public static function getActivePlanes2User($user_id)
    {
        $params = unserialize(self::getMembershipSetting());
        $expire = !empty($params['params']['expires']) ? $params['params']['expires'] * 86400 : 0;
        $time = time() - $expire;

        $db = Db::getConnection();
        $query = "SELECT mp.* FROM ".PREFICS."member_planes AS mp
                  LEFT JOIN ".PREFICS."member_maps AS mm ON mm.subs_id = mp.id
                  WHERE mm.status = 1 AND mm.end > $time AND mm.user_id = :user_id AND mm.successed = 0";

        $result = $db->prepare($query);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // ДАННЫЕ ПЛАНА ПОДПИСКИ ПО ID
    public static function getPlaneByID($id, $subscription_id = null)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."member_planes WHERE id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ?  $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ДЕЙСТВУЮЩИХ ПОДПИСОК ПОЛЬЗОВАТЕЛЯ С СОВПАДАЮЩИМ TG ЧАТОМ
     * @param $del_tg_chat
     * @param $user_id
     * @return bool|mixed
     */
    public static function countActiveSubsWithTgChat2User($del_tg_chat, $user_id) {
        $params = unserialize(self::getMembershipSetting());
        $expire = !empty($params['params']['expires']) ? $params['params']['expires'] * 86400 : 0;
        $time = time() - $expire;

        $db = Db::getConnection();
        $query = "SELECT COUNT(mm.id) FROM ".PREFICS.'member_maps AS mm
                  LEFT JOIN '.PREFICS."member_planes AS mp ON mm.subs_id = mp.id
                  WHERE mm.status = 1 AND mm.end > $time AND mm.user_id = :user_id AND mm.successed = 0
                  AND (mp.del_tg_chats LIKE '{$del_tg_chat},%' OR mp.del_tg_chats LIKE '%,{$del_tg_chat},%'
                  OR mp.del_tg_chats LIKE '%,{$del_tg_chat}' OR mp.del_tg_chats = '$del_tg_chat')";

        $result = $db->prepare($query);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch();

        return $data[0];
    }


    /**
     * ПРОВЕРИТЬ НАЛИЧИЕ ДЕЙСТВУЮЩИХ ПОДПИСОК ПОЛЬЗОВАТЕЛЯ С ГРУППОЙ НА УДАЛЕНИЕ
     * @param $group_id
     * @param $user_id
     * @return mixed
     */
    public static function checkActiveSubsWithDelGroup($group_id, $user_id) {
        $planes = self::getActivePlanes2User($user_id);
        if ($planes) {
            foreach ($planes as $plane) {
                $del_groups = $plane['del_groups'] ? unserialize($plane['del_groups']) : null;
                if ($del_groups && in_array($group_id, $del_groups)) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * ДОБАВИТЬ НОВЫЙ ПЛАН ПОДПИСКИ
     * @param $data
     * @return bool
     */
    public static function AddNewPlane($data)
    {
        $db = Db::getConnection();
        $fields = self::getFields(['id', 'create_new']);
        $data = self::beforeSave($fields, $data);
        $sql = Db::getInsertSQL($fields, PREFICS.'member_planes');
        $result = Db::bindParams($db, $sql, $fields, $data);

        return $result->execute();
    }



    /**
     * ИЗМЕНИТЬ ПЛАН ПОДПИСКИ
     * @param $id
     * @param $data
     * @return bool
     */
    public static function editPlane($id, $data)
    {
        $db = Db::getConnection();
        $fields = self::getFields(['id']);
        $data = self::beforeSave($fields, $data);
        $sql = Db::getUpdateSQL($fields, PREFICS.'member_planes', "id = $id");
        $result = Db::bindParams($db, $sql, $fields, $data);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ПЛАН ПОДПИСКИ
     * @param $id
     * @return bool
     */
    public static function DeletePlane($id)
    {
        $db = Db::getConnection();
        $time = time();
        // Проверить есть ли купленные действующие планы
        $result = $db->query("SELECT COUNT(*) FROM ".PREFICS."member_maps WHERE subs_id = $id AND end < $time");
        $count = $result->fetch();
        if ($count[0] > 0) {
            return false;
        }

        $sql = 'DELETE FROM '.PREFICS.'member_planes WHERE id = :id ; DELETE FROM '.PREFICS.'member_mess WHERE sibs_id = :id;';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * СПИСОК КУПЛЕННЫХ ПОДПИСОК
     * @param null $email
     * @return array|bool
     */
    public static function getMemberList($email = null)
    {
        $db = Db::getConnection();
        $query = "SELECT m_m.*, u.user_name, u.email, u.login FROM ".PREFICS."member_maps AS m_m
                  LEFT JOIN ".PREFICS."users AS u ON m_m.user_id = u.user_id";

        $where = $email ? " WHERE u.email LIKE '%$email%'" : '';
        $query .= "$where ORDER BY id DESC";
        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }

    /**
     * ПОЛУЧИТЬ СПИСОК ПОДПИСОК ПО ФИЛЬТРУ
     * @param $filter
     * @param int $page
     * @param null $show_items
     * @return array|bool
     */
    public static function getMemberListWithFilter($filter, $page = 1, $show_items = null, $is_pagination = false)
    {
        $db = Db::getConnection();
        $query = "SELECT mm.*, u.user_name, u.email, u.login FROM ".PREFICS."member_maps AS mm
                  LEFT JOIN ".PREFICS."users AS u ON mm.user_id = u.user_id";


        if ($filter) {
            $time = time();
            $clauses = [];
            if ($filter['email']) {
                $clauses[] = "u.email LIKE '%{$filter['email']}%'";
            }
            if ($filter['name']) {
                $clauses[] = "u.user_name LIKE '%{$filter['name']}%'";
            }
            if ($filter['surname']) {
                $clauses[] = "u.surname LIKE '%{$filter['surname']}%'";
            }
            if ($filter['plane']) {
                $clauses[] = "mm.subs_id = {$filter['plane']}";
            }
            if ($filter['status'] !== null) {
                $clauses[] = "mm.status = {$filter['status']}";
            }
            if ($filter['pay_status'] !== null) {
                if ($filter['pay_status']) {
                    $clauses[] = "mm.recurrent_cancelled = 0 OR mm.recurrent_cancelled IS NULL AND mm.subscription_id IS NOT NULL AND mm.subscription_id > 0";
                } else {
                    $clauses[] = "mm.recurrent_cancelled = 1";
                }
            }
            if ($filter['start']) {
                if ($filter['start'] == '1d') {
                    $clauses[] = "mm.begin < $time AND mm.begin >= " . (time() - 86400);;
                } elseif($filter['finish'] == '7d') {
                    $clauses[] = "mm.begin < $time AND mm.begin >= " . (time() - 604800);
                } elseif($filter['finish'] == '1m') {
                    $clauses[] = "mm.begin < $time AND mm.begin >= " . (time() - 2592000);
                } else {
                    if ($filter['start_from']) {
                        $clauses[] = "mm.begin >= {$filter['start_from']}";
                    }
                    if ($filter['start_to']) {
                        $clauses[] = "mm.begin < {$filter['start_to']}";
                    }
                }
            }
            if ($filter['finish']) {
                if ($filter['finish'] == '1d') {
                    $clauses[] = "mm.end >= $time AND mm.end < " . (time() + 86400);;
                } elseif($filter['finish'] == '7d') {
                    $clauses[] = "mm.end >= $time AND mm.end < " . (time() + 604800);
                } elseif($filter['finish'] == '1m') {
                    $clauses[] = "mm.end >= $time AND mm.end < " . (time() + 2592000);
                } else {
                    if ($filter['finish_from']) {
                        $clauses[] = "mm.end >= {$filter['finish_from']}";
                    }
                    if ($filter['finish_to']) {
                        $clauses[] = "mm.end < {$filter['finish_to']}";
                    }
                }
            }
            if ($filter['canceled']) {
                if ($filter['canceled'] == '1d') {
                    $clauses[] = "mm.rec_cancelled_date < $time AND mm.rec_cancelled_date >= " . (time() - 86400);;
                } elseif($filter['canceled'] == '7d') {
                    $clauses[] = "mm.rec_cancelled_date < $time AND mm.rec_cancelled_date >= " . (time() - 604800);
                } elseif($filter['canceled'] == '1m') {
                    $clauses[] = "mm.rec_cancelled_date < $time AND mm.rec_cancelled_date >= " . (time() - 2592000);
                } else {
                    if ($filter['canceled_from']) {
                        $clauses[] = "mm.rec_cancelled_date >= {$filter['canceled_from']}";
                    }
                    if ($filter['canceled_to']) {
                        $clauses[] = "mm.rec_cancelled_date < {$filter['canceled_to']}";
                    }
                }
                if ($filter['pay_status'] === null || !$filter['pay_status']) {
                    $clauses[] = "mm.recurrent_cancelled = 1";
                }
            }
            $where = !empty($clauses) ? (' WHERE ' . implode(" AND ", $clauses)) : '';
            $query .= $where . " ORDER BY mm.id DESC";
        }

        if ($show_items != null) {
            $offset = ($page - 1) * $show_items;
            $query .= $is_pagination ? " LIMIT $show_items OFFSET $offset" : '';
        }

        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }



    /**
     * ПОЛУЧИТЬ КОЛ-ВО ПОДПИСОК ПО ФИЛЬТРУ
     * @param array $filter
     * @return mixed
     */
    public static function getTotalPlanesWithFilter($filter = []) {

        $query = 'SELECT COUNT(id) FROM '.PREFICS.'member_maps AS mm
                  LEFT JOIN '.PREFICS.'users AS u ON u.user_id = mm.user_id';
        if ($filter) {
            $time = time();
            $clauses = [];
            if ($filter['email']) {
                $clauses[] = "u.email LIKE '%{$filter['email']}%'";
            }
            if ($filter['name']) {
                $clauses[] = "u.user_name LIKE '%{$filter['name']}%'";
            }
            if ($filter['surname']) {
                $clauses[] = "u.surname LIKE '%{$filter['surname']}%'";
            }
            if ($filter['plane']) {
                $clauses[] = "mm.subs_id = {$filter['plane']}";
            }
            if ($filter['status'] !== null) {
                $clauses[] = "mm.status = {$filter['status']}";
            }
            if ($filter['pay_status'] !== null) {
                if ($filter['pay_status']) {
                    $clauses[] = "mm.recurrent_cancelled = 0 OR mm.recurrent_cancelled IS NULL AND mm.subscription_id IS NOT NULL AND mm.subscription_id > 0";
                } else {
                    $clauses[] = "mm.recurrent_cancelled = 1";
                }
            }
            if ($filter['start']) {
                if ($filter['start'] == '1d') {
                    $clauses[] = "mm.begin < $time AND mm.begin >= " . (time() - 86400);;
                } elseif($filter['finish'] == '7d') {
                    $clauses[] = "mm.begin < $time AND mm.begin >= " . (time() - 604800);
                } elseif($filter['finish'] == '1m') {
                    $clauses[] = "mm.begin < $time AND mm.begin >= " . (time() - 2592000);
                } else {
                    if ($filter['start_from']) {
                        $clauses[] = "mm.begin >= {$filter['start_from']}";
                    }
                    if ($filter['start_to']) {
                        $clauses[] = "mm.begin < {$filter['start_to']}";
                    }
                }
            }
            if ($filter['finish']) {
                if ($filter['finish'] == '1d') {
                    $clauses[] = "mm.end >= $time AND mm.end < " . (time() + 86400);;
                } elseif($filter['finish'] == '7d') {
                    $clauses[] = "mm.end >= $time AND mm.end < " . (time() + 604800);
                } elseif($filter['finish'] == '1m') {
                    $clauses[] = "mm.end >= $time AND mm.end < " . (time() + 2592000);
                } else {
                    if ($filter['finish_from']) {
                        $clauses[] = "mm.end >= {$filter['finish_from']}";
                    }
                    if ($filter['finish_to']) {
                        $clauses[] = "mm.end < {$filter['finish_to']}";
                    }
                }
            }
            if ($filter['canceled']) {
                if ($filter['canceled'] == '1d') {
                    $clauses[] = "mm.rec_cancelled_date < $time AND mm.rec_cancelled_date >= " . (time() - 86400);;
                } elseif($filter['canceled'] == '7d') {
                    $clauses[] = "mm.rec_cancelled_date < $time AND mm.rec_cancelled_date >= " . (time() - 604800);
                } elseif($filter['canceled'] == '1m') {
                    $clauses[] = "mm.rec_cancelled_date < $time AND mm.rec_cancelled_date >= " . (time() - 2592000);
                } else {
                    if ($filter['canceled_from']) {
                        $clauses[] = "mm.rec_cancelled_date >= {$filter['canceled_from']}";
                    }
                    if ($filter['canceled_to']) {
                        $clauses[] = "mm.rec_cancelled_date < {$filter['canceled_to']}";
                    }
                }
                if ($filter['pay_status'] === null || !$filter['pay_status']) {
                    $clauses[] = "mm.recurrent_cancelled = 1";
                }
            }
            $where = !empty($clauses) ? (' WHERE ' . implode(" AND ", $clauses)) : '';
            $query .= $where;
        }

        $db = Db::getConnection();
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * ПОЛУЧИТЬ КОЛ-ВО ПОДПИСОК
     * @return mixed
     */
    public static function getTotalPlanes() {
        $db = Db::getConnection();
        $result = $db->query('SELECT COUNT(id) FROM '.PREFICS.'member_maps');
        $count = $result->fetch();

        return $count[0];
    }


    // ПОЛУЧИТЬ СПИСОК ДЕЙСТВУЮЩИХ ПОДПИСОК ЮЗЕРА
    public static function getPlaneListByUser($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT subs_id FROM ".PREFICS."member_maps WHERE user_id = $user_id AND status = 1");
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row['subs_id'];
        }
        
        return !empty($data) ? $data : false;
    }
	
	
	// ПОЛУЧИТЬ ВСЕ ПОДПИСКИ ЮЗЕРА
    public static function getAllPlanesByUser($user_id)
    {
        $date = time();
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."member_maps WHERE user_id = $user_id ORDER BY subs_id ASC";
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДЕЙСТВУЮЩИЕ ПЛАНЫ ПОДПИСОК ОДНОГО ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @param null $status
     * @param bool $is_only_id
     * @return array|bool
     */
    public static function getPlanesByUser($user_id, $status = null, $is_only_id = false)
    {
        $date = time();
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."member_maps WHERE user_id = $user_id AND end > $date";
        $sql .= ($status == 1 ? ' AND status = 1' : '').' ORDER BY subs_id ASC';
        $result = $db->query($sql);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $is_only_id ? $row['subs_id'] : $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ПЛАН ПОДПИСКИ ОДНОГО ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @param $subs_id
     * @return array|bool
     */
    public static function getPlane2User($user_id, $subs_id)
    {
        $date = time();
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."member_maps WHERE user_id = $user_id AND subs_id = $subs_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ РЕКУРРЕНТНЫЕ ПЛАНЫ ПОДПИСОК ОДНОГО ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @return array|bool
     */
    public static function getRecurrentPlanesByUser($user_id)
    {
        $db = Db::getConnection();
        $date = time();
        $result = $db->query("SELECT * FROM ".PREFICS."member_maps WHERE user_id = $user_id ORDER BY id DESC");

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // ПРОДЛИТЬ ПЛАН ПОДПИСКИ для РЕКУРРЕНТОВ
    public static function MemberProlong($subscription_id, $client_email = null, $name = null, $summ = null, $ip = null)
    {
        $db = Db::getConnection();
        $date = time();
        $result = $db->query(" SELECT * FROM ".PREFICS."member_maps WHERE subscription_id = '$subscription_id' AND status = 1 ");
        $member = $result->fetch(PDO::FETCH_ASSOC);
        if (isset($member) && !empty($member)) {

            if ($date - $member['last_update'] > 86000) { // если продление было больше чем 1 день назад

                // Получаем данные плана подписки и высчитываем период для продления
                $subscription = self::getPlaneByID($member['subs_id']);

                if ($subscription['period_type'] == 'Day') $ch = 86400;
                elseif ($subscription['period_type'] == 'Week') $ch = 604800;
                else $ch = date("t") * 86400;
                $period = $subscription['lifetime'] * $ch;
                $end = $member['end'] + $period;
                $count = $member['update_count'] + 1;

                $sql = 'UPDATE '.PREFICS."member_maps SET end = :end, last_update = :date, update_count = :count WHERE subscription_id = '$subscription_id'";
                $result = $db->prepare($sql);
                $result->bindParam(':date', $date, PDO::PARAM_INT);
                $result->bindParam(':count', $count, PDO::PARAM_INT);
                $result->bindParam(':end', $end, PDO::PARAM_INT);
                $res = $result->execute();

                if ($res) {
                    // Вызвать метод для создания заказа
                    if ($subscription['renewal_product'] != 0) {
                        $create = self::createOrderForProlong($subscription['renewal_product'], $member, $client_email, $name, $summ, $ip);
                    } else {
                        $create = false;
                    }
                    return $create;
                }
            } else {
                return true;
            }
        } else {
            // Если оплата прошла, но продлевать нечего - сообщаем админу
            $setting = System::getSetting();
            $email = $setting['admin_email'];
            $site = $setting['script_url'];
            $text = "<p>Была получена оплата, но подписка в BM не была продлена, проверьте что с ней</p>
            <p>Subscription_id : $subscription_id<br />Email: $client_email</p>
            <p><a href='$site'>$site</a></p>";
            $send = Email::SendMessageToBlank($email, 'BM', 'Подписка не продлена', $text);

            return false;
        }
    }



    // СОЗДАТЬ ЗАКАЗ ПРИ ПРОДЛЕНИИ ПОДПИСКИ
    public static function createOrderForProlong($product_id, $member, $client_email, $name, $summ, $ip)
    {
        // Подготовить данные для записи заказа в БД
        $date = time();

        while(Order::checkOrderDate($date)) {
            $date = $date + 1;
        }

        $user = User::getUserDataByEmail($client_email);

        $product = Product::getProductByID($product_id);
        if ($product) {
            $sale_id = null;
            $base_id = 0;

            // Данные для партнёрки
            $partner_id = null;

            $extension = System::CheckExtensension('partnership', 1);
            if ($extension) {
                if (!empty($user['from_id'])) {
                    $aff_set = unserialize(System::getExtensionSetting('partnership'));
                    $aff_life = intval($aff_set['params']['aff_life']) * 86400;

                    $period = time() - $user['reg_date'];
                    $from_id = $period < $aff_life ? intval($user['from_id']) : false;
                } else {
                    $from_id = false;
                }

                if ($from_id) {
                    $verify = Aff::PartnerVerify($from_id); // Проверка партнёра на существование и самозаказ
                    if ($verify) {
                        $partner_id = $from_id;
                    }
                }
            }

            $index = null;
            $city = null;
            $address = null;
            $comment = null;
            $status = 0;
            $var = null;

            $param = "$date;0;;/";

            $add_order = Order::addOrder($product['product_id'], $summ, $name, $client_email, $user['phone'], $index, $city,
                $address, $comment, $param, $partner_id, $date, $sale_id, $status, $base_id, $var, $product['type_id'],
                $product['product_name'], $ip, 0, null, null, null, 0,
                null, 1
            );

            if ($add_order) {
                $order = Order::getOrderData($date, 0);

                if ($order) {
                     return Order::renderOrder($order) ? $order['order_id'] : false;
                }
            }
            return false;
        } else {
            return false;
        }

        // Записать заказ в БД и обработать.
    }


    // ДОБАВИТЬ или Обновить УЧАСТНИКА
    // Принимает id юзера, id плана подписки и статус = 1
    // Сценарий возникает при создании подписки (любым способом) и её продлении через покупку продукта
    // Продление через рекурренты проходит через другой метод
    public static function addMember($plane_id, $user_id, $status = 1, $subscription_id = null,  $map_id = 0)
    {
        $db = Db::getConnection();
        $date = time();
        $new_sub = self::getPlaneByID($plane_id);
		$related_planes = !empty($new_sub['related_planes']) ? $new_sub['related_planes'] : false;

        // Расчёт периода продления
        if ($new_sub['period_type'] == 'Day') {
            $ch = 86400; // сек в дне
        } elseif ($new_sub['period_type'] == 'Week') {
            $ch = 604800; // сек в неделе
        } else {
            $ch = date("t") * 86400; // сек в тек месяце
        }

        $period = $new_sub['lifetime'] * $ch; // на сколько продляем
        $max_periods = $new_sub['max_periods'];
        $end = $date + $period; // Дата окончания после продления
        
        
        // новый метод 
        $create_new = false;
        
        // Если это продление
        if($map_id != 0) {
            $current_sub = self::getUserMemberMapByID($map_id); // Получение подписки с любым статусом
            $create_new = false;
            
        } else {
            // Если это покупка или что-то другое
            $now_status = false;
            $current_sub = self::getUserSubscribe($plane_id, $user_id, $related_planes, $now_status); // Проверить существование любой подписки, даже не активной
            if($current_sub){
                // если активная подписка есть
                if($current_sub['status'] == 1){
                    if($current_sub['create_new'] == 1){
                        if($current_sub['subs_id'] == $plane_id ){
                            // создать новую подписку
                            $create_new = 1;
                        } 
                    }      
                } else {
                   // Если подписка закончена, а в плане указано продлевать только активную
                   if($current_sub['prolong_active'] == 1)$create_new = 1;
                }
            }
        }
        
        // ОБНОВЛЕНИЕ или СОЗДАНИЕ ПОДПИСКИ
        if ($current_sub && !$create_new){ // Если подписка есть и не надо создавать новую, то обновить текущую
            
            // Если подписка кончилась, то дату считаем от сейчас, иначе от даты окончания тек. подписки
            if($current_sub['status'] == 0) $end = $date + $period;
            else $end = $current_sub['end'] + $period;
            
            $count = $current_sub['update_count'] == null ? 1 : $current_sub['update_count'] + 1;
            $successful = $max_periods == $count ? 1 : 0; // Проверка на максимальное кол-во периодов

            $result = self::updUserSubscribe($current_sub['subs_id'], $user_id, $end, $date, $status, $count, $successful);

			$log = self::writeMemberLog($user_id, $plane_id, $current_sub['id'], $current_sub['end'], $end, $period, $date);

        } else { // Если нет, то создать и учесть задержку
            if ($new_sub['delay'] > 0) {
                // рассчитываем задержку
                $end = $date + $new_sub['delay'] * 86400;
            }

            $result = self::addUserSubscribe($plane_id, $user_id, $status, $date, $end, $subscription_id);
			$log = self::writeMemberLog($user_id, $plane_id, 0, 0, $end, $period, $date);
        }

        if ($result && $status && Telegram::getStatus() && $new_sub['del_tg_chats']) {
            Telegram::delUserFromBlacklist($user_id, $new_sub['del_tg_chats']);
        }

        return $result;
    }
    
    
    
    
    
    // ПОЛУЧИТЬ ПОДПИСКУ ЮЗЕРА по map_id
    public static function getUserMemberMapByID($map_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."member_maps WHERE id = $map_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)) return $data;
        else return false;
    }
    


    /**
     * ДОБАВИТЬ ПОДПИСКУ ДЛЯ ПОЛЬЗОВАТЕЛЯ
     * @param $plane_id
     * @param $user_id
     * @param $status
     * @param $date
     * @param $end
     * @param $subscribe_id
     * @return bool
     */
    public static function addUserSubscribe($plane_id, $user_id, $status, $date, $end, $subscribe_id) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'member_maps (subs_id, user_id, begin, end, status, create_date, subscription_id) 
                VALUES (:subs_id, :user_id, :begin, :end, :status, :create_date, :subscription_id)';

        $result = $db->prepare($sql);
        $result->bindParam(':subs_id', $plane_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':create_date', $date, PDO::PARAM_INT);
        $result->bindParam(':begin', $date, PDO::PARAM_INT);
        $result->bindParam(':end', $end, PDO::PARAM_INT);
        $result->bindParam(':subscription_id', $subscribe_id, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДЕЙСТВУЮЩУЮ ПОДПИСКУ ПОЛЬЗОВАТЕЛЯ
     * @param $plane_id
     * @param $user_id
     * @param bool $related_planes
     * @return bool|mixed
     */
    public static function getUserSubscribe($plane_id, $user_id, $related_planes = false, $status = 1) {
        $db = Db::getConnection();
        if($status) $status = " AND status = $status ";
        $where = 'WHERE '.($related_planes ? "mm.subs_id IN ($related_planes)" : "mm.subs_id = $plane_id")." AND user_id = $user_id $status LIMIT 1";
        $sql = "SELECT mm.*, mp.* FROM ".PREFICS."member_maps AS mm LEFT JOIN ".PREFICS."member_planes AS mp ON mm.subs_id = mp.id $where";
        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        return !empty($data) ? $data : false;
    }
    
    
    
    // ПОЛУЧИТЬ ВСЕ ПОДПИСКИ ЮЗЕРА и УЗНАТЬ ЕСТЬ ЛИ АКТИВНЫЕ
    public static function getSubscribePlanesByUser($plane_id, $user_id, $related_planes)
    {
        $db = Db::getConnection();
        $where = 'WHERE '.($related_planes ? "subs_id IN ($related_planes)" : "subs_id = $plane_id")." AND user_id = $user_id";
        $sql = "SELECT * FROM ".PREFICS."member_maps $where";
        $result = $db->query($sql);
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row;
        }
        $status = 2; // есть неактивные подписки
        
        if(!empty($data)){
            foreach($data as $item){
                if($item['status'] == 1) $status = 1; // есть активные подписки
            }
        } else $status = 0; // нет подписок совсем
        
        return $status;
    }


    /**
     * ОБНОВИТЬ ПОДПИСКУ ПОЛЬЗОВАТЕЛЯ
     * @param $plane_id
     * @param $user_id
     * @param $end
     * @param $date
     * @param $status
     * @param $count
     * @param $successful
     * @return bool
     */
    public static function updUserSubscribe($plane_id, $user_id, $end, $date, $status, $count, $successful) {
        $db = Db::getConnection();
		$send_notification = 0;
        $sql = 'UPDATE '.PREFICS."member_maps SET end = :end, last_update = :date, status = :status, update_count = :count,
                successed = :successed, send_notification = :send_notification WHERE subs_id = :subs_id AND user_id = :user_id";

        $result = $db->prepare($sql);
        $result->bindParam(':subs_id', $plane_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':end', $end, PDO::PARAM_INT);
        $result->bindParam(':date', $date, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':count', $count, PDO::PARAM_INT);
        $result->bindParam(':successed', $successful, PDO::PARAM_INT);
		$result->bindParam(':send_notification', $send_notification, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ЗАПИСЬ В ЛОГ МЕМБЕРШИПА
     * @param $user_id
     * @param $plane_id
     * @param $subs_map_id
     * @param $end_before
     * @param $end_after
     * @param $time_prolong
     * @param $date
     * @return bool
     */
    public static function writeMemberLog($user_id, $plane_id, $subs_map_id, $end_before, $end_after, $time_prolong, $date)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'member_log (user_id, plane_id, subs_map_id, end_before, end_after, time_prolong, date ) 
                VALUES (:user_id, :plane_id, :subs_map_id, :end_before, :end_after, :time_prolong, :date)';

        $result = $db->prepare($sql);
  		$result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':plane_id', $plane_id, PDO::PARAM_INT);
        $result->bindParam(':subs_map_id', $subs_map_id, PDO::PARAM_INT);
        $result->bindParam(':end_before', $end_before, PDO::PARAM_INT);
        $result->bindParam(':end_after', $end_after, PDO::PARAM_INT);
        $result->bindParam(':time_prolong', $time_prolong, PDO::PARAM_INT);
        $result->bindParam(':date', $date, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ЛОГОВ МЕМБЕРШИПА
     * @param $filter
     * @return mixed
     */
    public static function getTotalMemberLog($filter) {
        $clauses = [];
        if ($filter['is_filter']) {
            if ($filter['subs_map_id']) {
                $clauses[] = "subs_map_id = {$filter['subs_map_id']}";
            }
            if ($filter['plane_id']) {
                $clauses[] = "plane_id = {$filter['plane_id']}";
            }
            if ($filter['user_id']) {
                $clauses[] = "user_id = {$filter['user_id']}";
            }
            if ($filter['start_date']) {
                $clauses[] = "date >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "date < {$filter['finish_date']}";
            }
        }
        $where = !empty($clauses) ? 'WHERE ' .implode(' AND ', $clauses) : '';

        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."member_log $where");

        $data = $result->fetch();

        return $data[0];
    }


    /**
     * ПОЛУЧИТЬ ЛОГ МЕМБЕРШИПА
     * @param $filter
     * @param $page
     * @param $show_items
     * @return array|bool
     */
    public static function getMemberLog($filter, $page, $show_items)
    {
        $clauses = [];
        if ($filter['is_filter']) {
            if ($filter['subs_map_id']) {
                $clauses[] = "subs_map_id = {$filter['subs_map_id']}";
            }
            if ($filter['plane_id']) {
                $clauses[] = "plane_id = {$filter['plane_id']}";
            }
            if ($filter['user_id']) {
                $clauses[] = "user_id = {$filter['user_id']}";
            }
            if ($filter['start_date']) {
                $clauses[] = "date >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "date < {$filter['finish_date']}";
            }
        }
        $where = !empty($clauses) ? 'WHERE ' .implode(' AND ', $clauses) : '';
        $query = "SELECT * FROM ".PREFICS."member_log $where ORDER BY id DESC";

        if ($show_items != null) {
            $offset = ($page - 1) * $show_items;
            $query .= " LIMIT $show_items OFFSET $offset";
        }

        $db = Db::getConnection();
        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        	$data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * УДАЛЕНИЕ СТАРЫХ ЛОГОВ
     * @param $date
     * @return bool
     */
    public static function delOldLogs($date) {
        $db = Db::getConnection();
        $query = 'DELETE FROM '.PREFICS.'member_log WHERE date < :date;
                  DELETE FROM '.PREFICS.'member_mess_log WHERE date < :date';
        $result = $db->prepare($query);
        $result->bindParam(':date', $date, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * СОЗДАТЬ ПИСЬМА ОБ ОКОНЧАНИИ ПОДПИСКИ
     * @param $subscription
     * @param $end
     * @param $name
     * @param $email
     * @param $phone
     * @param $plane
     */
    public static function AddMessAboutExpirePlane($subscription, $end, $name, $email, $phone, $plane) {
        // Создать письма
        if (!empty($subscription['letter_1'])) {
            $send_time = $end - ($subscription['letter_1_time'] * 3600);
            self::addMessage2Send($name, $email, $phone, $plane, $send_time, 1, 1);
        }

        if (!empty($subscription['letter_2'])) {
            $send_time = $end - ($subscription['letter_2_time'] * 3600);
            self::addMessage2Send($name, $email, $phone, $plane, $send_time, 2, 1);
        }

        if (!empty($subscription['letter_3'])) {
            $send_time = $end - ($subscription['letter_3_time'] * 3600);
            self::addMessage2Send($name, $email, $phone, $plane, $send_time, 3, 1);
        }
    }


    /**
     * @param $name
     * @param $email
     * @param $phone
     * @param $plane
     * @param $send_time
     * @param $letter_num
     * @param $status
     * @return bool
     */
    public static function addMessage2Send($name, $email, $phone, $plane, $send_time, $letter_num, $status) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'member_mess (subs_id, email, phone, name, send_time, letter_num, status)
                VALUES (:subs_id, :email, :phone, :name, :send_time, :letter_num, :status)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':subs_id', $plane, PDO::PARAM_INT);
        $result->bindParam(':send_time', $send_time, PDO::PARAM_INT);
        $result->bindParam(':letter_num', $letter_num, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ УЧАСТНИКА по ID подписки и удалить его письма.
     * @param $id
     * @return bool
     */
    public static function delMember($id)
    {
        $db = Db::getConnection();
        $row = self::getMemberRow($id); // получили данные записи участника
        $user = User::getUserById($row['user_id']); // получили данные юзера
        $email = $user['email'];
        $subs_id = $row['subs_id'];

        $sql = 'DELETE FROM '.PREFICS.'member_maps WHERE id = :id;
                DELETE FROM '.PREFICS.'member_mess WHERE subs_id = :subs_id AND email = :email';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':subs_id', $subs_id, PDO::PARAM_INT);
        $result->bindParam(':email', $email, PDO::PARAM_STR);

        return $result->execute();
    }


    // ДАННЫЕ ЗАПИСИ В КАРТЕ УЧАСТНИКОВ
    public static function getMemberRow($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."member_maps WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    // ИЗМЕНИТЬ ДАННЫЕ ПОДПИСКИ ЮЗЕРА
    public static function editUserSubscript($id, $subscription_id, $end, $plane_id, $status, $recurrent_cancelled, $lc_id = 0)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'member_maps SET subscription_id = :subscription_id, end = :end, subs_id = :subs_id,
                status = :status, recurrent_cancelled = :recurrent_cancelled, lc_id = :lc_id WHERE id = '.$id;

        $result = $db->prepare($sql);
        $result->bindParam(':subscription_id', $subscription_id, PDO::PARAM_STR);
        $result->bindParam(':end', $end, PDO::PARAM_INT);
        $result->bindParam(':subs_id', $plane_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':lc_id', $lc_id, PDO::PARAM_INT);
        $result->bindParam(':recurrent_cancelled', $recurrent_cancelled, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ УЧАСТНИКА по емейл и ID плана
     * @param $user_id
     * @param $subs_id
     * @return bool
     */
    public static function delMemberByEmail($user_id, $subs_id)
    {
        $db = Db::getConnection();

        // сначала получить его планы
        $result = $db->query("SELECT id, subscription_id, recurrent_cancelled FROM ".PREFICS."member_maps WHERE status = 1 AND user_id = ".$user_id);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        if (!empty($data)) {
            foreach($data as $sub) {
                // удалить рекуренты
                if ($sub['subscription_id'] != null && $sub['recurrent_cancelled'] == null) {

                    $stop_recurr = self::cancelCloudPayments($sub['subscription_id']);
                }

                // Получить данные плана подписки по id
                $plane = self::getPlaneByID($subs_id);

                if ($plane['manager_letter'] != null) {
                    $manager_letter = unserialize(base64_decode($plane['manager_letter']));

                    if (isset($manager_letter['email_manager']) && !empty($manager_letter['email_manager'])) {

                        if (isset($manager_letter['subj_manager'])) {
                            $subj_manager = $manager_letter['subj_manager'];
                        } else {
                            $subj_manager = null;
                        }

                        if (isset($manager_letter['letter_manager'])) {
                            $letter_manager = $manager_letter['letter_manager'];
                        } else {
                            $letter_manager = null;
                        }

                        $send_custom = Email::sendLetterAboutExpireSubscription($manager_letter['email_manager'], $subj_manager, $letter_manager, $user_id);
                    }
                }

                // Перебрать id групп которые нужно удалить при окончании подписки
                if (!empty($plane['del_groups'])) {
                    $groups = unserialize($plane['del_groups']);
                    $del_groups = User::deleteUserGroupsFromList($user_id, $groups); // удалить из user_groups_map
                }
            }
        }

        // удалить подписки юзера

        $user = User::getUserById($user_id);
        $email = $user['email'];
        $sql = 'DELETE FROM '.PREFICS.'member_maps WHERE subs_id = :subs_id AND user_id = :user_id';
        $result = $db->prepare($sql);
        $result->bindParam(':subs_id', $subs_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    // НАЙТИ ПОДПИСКУ В КАРТЕ ПО Subcsription_id
    public static function getSubscriptionRecurrent($subs_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."member_maps WHERE subscription_id = '$subs_id' LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data: false;
    }


    // ОСТАНОВИТЬ \ ВОЗОБНОВИТЬ ПЛАН ПОДПИСКИ
    public static function pauseMember($id, $status)
    {
        $db = Db::getConnection();

        $row = self::getMemberRow($id);
        $user = User::getUserById($row['user_id']);
        $email = $user['email'];
        $subs_id = $row['subs_id'];

        $result = $db->query(" SELECT * FROM ".PREFICS."member_maps WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (isset($data) && !empty($data) && $data['subscription_id'] != null) {
            if ($status == 0) {
                $pause = self::cancelCloudPayments($data['subscription_id']);
            }

            // Получить данные подписки по id чтобы удалить группы юзера
            //$plane = self::getPlaneByID($data['subs_id']);

            // Перебрать id групп которые нужно удалить при окончании подписки
            /*
            if (!empty($plane['del_groups'])) {
                $groups = unserialize($plane['del_groups']);
                User::deleteUserGroupsFromList($row['user_id'], $groups); // удалить из user_groups_map
            }
            */
        }

        if ($data['subscription_id'] != null) {
            $recurrent_cancelled = 1;
            $rec_cancelled_date = time();
            $sql = 'UPDATE '.PREFICS.'member_maps SET recurrent_cancelled = :recurrent_cancelled,
                    rec_cancelled_date = :rec_cancelled_date WHERE id = :id';

            $result = $db->prepare($sql);
            $result->bindParam(':recurrent_cancelled', $recurrent_cancelled, PDO::PARAM_INT);
            $result->bindParam(':rec_cancelled_date', $rec_cancelled_date, PDO::PARAM_INT);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();

        } /*else {
            $sql = 'UPDATE '.PREFICS.'member_maps SET status = :status WHERE id = :id ;
            UPDATE '.PREFICS.'member_mess SET status = :status WHERE subs_id = :subs_id AND email = :email  ';

            $result = $db->prepare($sql);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            $result->bindParam(':subs_id', $subs_id, PDO::PARAM_INT);
            $result->bindParam(':email', $email, PDO::PARAM_STR);
            return $result->execute();
        }
        */
    }


    public static function cancelCloudPayments($subscription_id)
    {
        $url = 'https://api.cloudpayments.ru/subscriptions/cancel';

        $request=array(
                'Id'=>$subscription_id,
            );
        $payment = Order::getPaymentSetting('cloudpayments');
        $params = unserialize(base64_decode($payment['params']));
        $public_id = $params['public_id'];
        $pass_api = $params['pass_api'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch,CURLOPT_USERPWD,$public_id . ":" . $pass_api);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        $content = json_decode($content);

        return $content;
    }


    /**
     * ОТПРАВКА ПИСЕМ КЛИЕНТУ ОБ ОКОНЧАНИЯ ДЕЙСТВИЯ ПОДПИСКИ
     * @return bool
     */
    public static function SendExpirationMessage()
    {
        // Получить список сообщений для отправки 
        $db = Db::getConnection();
        $setting = System::getSetting();
        $time = time();
        $result = $db->query("SELECT * FROM ".PREFICS."member_mess WHERE send_time < $time AND status = 1");
    
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        if (!empty($data)) {
            foreach($data as $mess) {
                $plane = Member::getPlaneByID($mess['subs_id']); // Получить данные плана по ID
                $num = $mess['letter_num'];
                $subj_index = "letter_{$mess['letter_num']}_subj";
                $let_index = "letter_{$mess['letter_num']}";
                $sms_status_index = "sms{$mess['letter_num']}_status";
                $sms_text_index = "sms{$mess['letter_num']}_text";

                if ($plane['renewal_type'] != 3) {
                    $product = Product::getProductById($plane['renewal_product']);
                    $link = $plane['renewal_type'] == 1 ? "{$setting['script_url']}/buy/{$product['product_id']}" :
                        "{$setting['script_url']}/catalog/{$product['alias']}";
                } else {
                    $link = $plane['renewal_link'];
                }
            
                // Отправить письмо клиенту
                $send = Email::SendExpirationMessageByClient($mess['email'], $mess['name'], $plane[$subj_index],
                    $plane[$let_index], $link
                );

                if ($send) { // Если отправлено, то
                    if ($plane[$sms_status_index] && $mess['phone']) {
                        SMS::sendNotice2ExpireSubs($mess['name'], $link, $mess['phone'], $plane[$sms_text_index]);
                    }

                    // Записать в лог
                    $sql = 'INSERT INTO '.PREFICS.'member_mess_log (mess_id, subs_id, email, send_time, letter_num )
                            VALUES (:mess_id, :subs_id, :email, :send_time, :letter_num)';
                    
                    $result = $db->prepare($sql);
                    $result->bindParam(':email', $mess['email'], PDO::PARAM_STR);
                    $result->bindParam(':mess_id', $mess['id'], PDO::PARAM_INT);
                    $result->bindParam(':subs_id', $mess['subs_id'], PDO::PARAM_INT);
                    $result->bindParam(':send_time', $mess['send_time'], PDO::PARAM_INT);
                    $result->bindParam(':letter_num', $mess['letter_num'], PDO::PARAM_INT);
                    $result->execute();
                    
                    // Удалить сообщение
                    return self::delMessages($mess['id']);
                } else {
                    return false;
                }
            }
        }
    }


    /**
     * УДАЛИТЬ СООБЩЕНИЯ
     * @param $id
     * @param null $plane_id
     * @param null $email
     * @return bool
     */
    public static function delMessages($id, $plane_id = null, $email = null) {
        $where = "WHERE " . ($id ? 'id = :id' : 'subs_id = :subs_id AND email = :email');
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS."member_mess $where");

        if ($id) {
            $result->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $result->bindParam(':subs_id', $plane_id, PDO::PARAM_INT);
            $result->bindParam(':email', $email, PDO::PARAM_STR);
        }

        return $result->execute();
    }


    // УДАЛИТЬ ВСЕ ПОДПИСКИ ЮЗЕРА по ID
    //TODO Это глобальная тудушка Удалить из базы лишние таблицы и связанный с ними функционал из кода
    public static function delMemberByIDUser($IDuser)
    {
        $db = Db::getConnection();  
        $sql = 'DELETE FROM '.PREFICS.'member_maps WHERE user_id = :IDuser;';
        $result = $db->prepare($sql);
        $result->bindParam(':IDuser', $IDuser, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    // СОХРАНИТЬ НАСТРОЙКИ МЕМБЕРШИПА
    public static function SaveBlogSetting($params, $status)
    {
        $db = Db::getConnection();  
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'membership'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
        // ПОЛУЧИТЬ НАСТРОЙКИ МЕМБЕРШИПА
    public static function getMembershipSetting()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT params FROM ".PREFICS."extensions WHERE name = 'membership' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data['params'] : false;
    }
    
    
    
    // Получить статус МЕМБЕРШИПА
    public static function getMemberShipStatus()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT enable FROM ".PREFICS."extensions WHERE name = 'membership' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data['enable'] : false;
    }
    
    
    
     /**
     *   УРОВНИ ДОСТУПА
     
    // СПИСОК УРОВНЕЙ
    public static function getLevelsList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."member_levels ORDER BY id ASC");
        $i = 0;
        while($row = $result->fetch()) {
            $data[$i]['id'] = $row['id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['level_desc'] = $row['level_desc'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if (isset($data)) return $data;
        else return false;
    }
    
    
    
    // СОЗДАТЬ НОВЫЙ УРОВЕНЬ ДОСТУПА
    public static function AddNewLevel($name, $desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'member_levels (name, level_desc ) 
                VALUES (:name, :level_desc)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':level_desc', $desc, PDO::PARAM_STR);
        return $result->execute();
    }
    */
}