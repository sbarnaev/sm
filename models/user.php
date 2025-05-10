<?php defined('BILLINGMASTER') or die;


class User {

    use ResultMessage;

    // СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ + ВОЗВРАТ ЕГО ID
    // С проверкой существования по емейлу и обновлением is_client, если потом покупается платный продукт
    // И с отправкой доступа для клиента

    public static function AddNewClient($name, $email, $phone, $city, $address, $zip_code, $role, $is_client, $reg_date,
                                        $enter_method, $param, $status, $hash, $password, $send_login, $letter, $is_subs = 0,
                                        $login = null,  $from_id = null, $surname = null, $nick_telegram = null, $nick_instagram = null)
    {
        $param = explode(";", $param);
        $reg_key = md5($reg_date);
        $enter_time = intval($param[0]);
        $refer = isset($param[1]) ? $param[1] : null;
        $channel_id = isset($param[2]) ? intval($param[2]) : null;

        if (!$hash) {
            $pass_data = System::createPass();
            $password = $pass_data['pass'];
            $hash = $pass_data['hash'];
        }

        $user_id = self::addUser($name, $email, $phone, $hash, $reg_date, $role, $enter_method, $enter_time, $reg_key,
            $status, $login, $surname, $city, $address, $zip_code, $is_client, $refer, $channel_id, $is_subs, $from_id,
            $nick_telegram, $nick_instagram);


        if ($user_id) {
            if ($send_login == 1 || $send_login == 2) {
                Email::SendLogin($name, $email, $password, $letter);

                if ($phone) {
                    SMS::send2UserRegistration($phone, $name, $email, $password);
                }
            }

            if ($from_id || $from_id = System::getPartnerId($email)) {
                PostBacks::sendData(PostBacks::ACT_TYPE_USER_REGISTRATION, $from_id, $name, $email, $phone, $user_id);
            }

            return self::getUserDataByEmail($email, null);
        }

        return  false;
    }

    // ОБНОВИТЬ ДАННЫЕ КЛИЕНТА
    public static function updateClientData($user_id, $name, $is_client, $is_subs, $surname, $city, $hash = null, $phone = null)
    {
        $db = Db::getConnection();

        if($phone){
            $sql = 'UPDATE '.PREFICS.'users SET user_name = :user_name, phone = :phone';
        } else {
            $sql = 'UPDATE '.PREFICS.'users SET user_name = :user_name';
        }
        $sql .= $hash ? ', pass = :pass' : '';
        $sql .= $is_client == 1 ? ', is_client = 1' : '';
        $sql .= $is_subs == 1 ? ', is_subs = 1' : '';
        $sql .= $surname ? ', surname = :surname' : '';
        $sql .= $city ? ', city = :city' : '';
        $sql .= ' WHERE user_id = '.intval($user_id);

        $result = $db->prepare($sql);
        $result->bindParam(':user_name', $name, PDO::PARAM_STR);
        if($phone){
            $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        }
        if ($hash) {
            $result->bindParam(':pass', $hash, PDO::PARAM_STR);
        }
        if ($surname) {
            $result->bindParam(':surname', $surname, PDO::PARAM_STR);
        }   
        if ($city) {
            $result->bindParam(':city', $city, PDO::PARAM_STR);
        }   

        return $result->execute();
    }

    public static function updateClientStatus($user_id, $is_client) {
        $db = Db::getConnection();

        $sql = "UPDATE ".PREFICS."users SET is_client = :is_client WHERE user_id = ".intval($user_id);
        $result = $db->prepare($sql);
        $result->bindParam(':is_client', $is_client, PDO::PARAM_INT);

        return $result->execute();
    }

    // НАЙТИ ПОЛЬЗОВАТЕЛЯ
    public static function searchUser($email)
    {
        $db = Db::getConnection();

        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."users WHERE email = '$email'");
        $count = $result->fetch();

        return $count[0] > 0 ? $count[0] : false;
    }


    // ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ
    public static function addUser($name, $email, $phone, $hash, $reg_date, $role, $enter_method = null, $enter_time = null,
                                   $reg_key = null, $status = null, $login = null, $surname = null, $city = null, $address = null,
                                   $zip_code = null, $is_client = null, $refer = null, $channel_id = null, $is_subs = 0, $from_id = null,
                                   $nick_telegram = null, $nick_instagram = null)
    {
        $db = Db::getConnection();

        $sql = 'INSERT INTO '.PREFICS.'users (user_name, email, phone, city, address, zipcode, role, is_client, reg_date, enter_method, enter_time,
                    refer, reg_key, status, pass, channel_id, login, from_id , surname, nick_telegram, nick_instagram, is_subs)
                    VALUES (:name, :email, :phone, :city, :address, :zipcode, :role, :is_client, :reg_date, :enter_method, :enter_time,
                    :refer, :reg_key, :status, :pass, :channel_id, :login, :from_id, :surname, :nick_telegram, :nick_instagram, :is_subs)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':city', $city, PDO::PARAM_STR);
        $result->bindParam(':address', $address, PDO::PARAM_STR);
        $result->bindParam(':zipcode', $zip_code, PDO::PARAM_STR);
        $result->bindParam(':role', $role, PDO::PARAM_STR);
        $result->bindParam(':is_client', $is_client, PDO::PARAM_INT);
        $result->bindParam(':reg_date', $reg_date, PDO::PARAM_INT);
        $result->bindParam(':enter_method', $enter_method, PDO::PARAM_STR);
        $result->bindParam(':enter_time', $enter_time, PDO::PARAM_INT);
        $result->bindParam(':refer', $refer, PDO::PARAM_STR);
        $result->bindParam(':reg_key', $reg_key, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':channel_id', $channel_id, PDO::PARAM_INT);
        $result->bindParam(':pass', $hash, PDO::PARAM_STR);
        $result->bindParam(':login', $login, PDO::PARAM_STR);
        $result->bindParam(':from_id', $from_id, PDO::PARAM_INT);
        $result->bindParam(':surname', $surname, PDO::PARAM_STR);
        $result->bindParam(':nick_telegram', $nick_telegram, PDO::PARAM_STR);
        $result->bindParam(':nick_instagram', $nick_instagram, PDO::PARAM_STR);
        $result->bindParam(':is_subs', $is_subs, PDO::PARAM_INT);
        $result = $result->execute();

        return $result ? $db->lastInsertId() : false;
    }

    public static function importUsers($user_name, $email, $phone, $send_letter, $subs_key, $user_param, $setting, 
                                    $empty_name, $letter, $responder, $time, $groups, $validate, $is_client, $surname, $is_subs, $city)
    {
        // валидировать емейл
        if ($validate == 1 && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // создать юзера и  пароль
        $user_name = $user_name ?: $empty_name;
        $confirmed = 1;

        if (!$client = self::getUserDataByEmail($email, null)) { // Если пользователя еще нет в базе
            // Добавить пользователя в базу
            $client = User::AddNewClient($user_name, $email, $phone, $city, null, null,
                'user',  $is_client, $time, 'custom', $user_param, $confirmed, null,
                null, $send_letter, $letter, $is_subs, null, null, $surname
            );
        } else {
            $password = $hash = null;

            if ($send_letter == 2) { // Если нужно отправить всем пользователям письмо
                $pass_data = System::createPass();
                $password = $pass_data['pass'];
                $hash = $pass_data['hash'];
            }

            // Обновить данные пользователя
            $result = User::updateClientData($client['user_id'], $user_name, $is_client, $is_subs, $surname, $city, $hash, $phone);

            if ($result && $password) {
                Email::SendLogin($user_name, $email, $password, $letter);
            }
        }

        if ($client) {
            if ($groups) { // Добавить юзеру группу
                $groups = explode(',',$groups[0]);
                foreach($groups as $group) {
                    self::WriteUserGroup($client['user_id'], $group);
                }
            }

            if ($responder != 0) { // Подписать на автосерию
                // Получить письма автосерии
                $letter_list = Responder::getAutoLetterList($responder);

                $subs_key = md5($email . $time);
                $cancelled = 0;
                $spam = 0;
                $param = time().';0;;/import';

                $responder_setting = unserialize(Responder::getResponderSetting());

                $add = Responder::addSubsToMap($responder, $email, $user_name, $phone, $time, $subs_key, $confirmed, $cancelled, $spam, $param, $responder_setting, $setting);

                if ($letter_list) {
                    foreach($letter_list as $letter) {
                        $send = time() + ($letter['send_time'] * 3600);
                        $status = 0;
                        $task = Responder::AddTask($responder, $letter['letter_id'], $email, $send, $status);
                    }
                }
            }

            return $client;
        }

    }




    // СПИСОК ВСЕХ ЮЗЕРОВ ДЛЯ ЭКСПОРТА
    public static function getAllUsers($groups = null)
    {
        $db = Db::getConnection();

        $query = "SELECT * FROM ".PREFICS."users";

        if ($groups === false) {
            $query .= " WHERE user_id NOT IN (SELECT user_id FROM ".PREFICS."user_groups_map GROUP BY user_id)";
        } elseif ($groups) {
            $query .= " WHERE user_id IN (SELECT user_id FROM ".PREFICS."user_groups_map WHERE group_id IN ($groups))";
        }

        $query .= ' ORDER BY user_id DESC';
        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // ПОЛУЧИТЬ СПИСОК МЕНЕДЖЕРОВ
    public static function getManagerList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT user_id, user_name, email FROM ".PREFICS."users WHERE role = 'manager' AND status = 1 ORDER BY user_id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['user_id'] = $row['user_id'];
            $data[$i]['user_name'] = $row['user_name'];
            $data[$i]['email'] = $row['email'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }




    // ПОЛУЧИТЬ УРОВЕНЬ ДОСТУПА для Менеджера
    public static function getACLbyUserID($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."acl WHERE user_id = $user_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
    }




    // ОБНОВИТЬ СТАТУС ЮЗЕРА ПО email
    public static function UpdateUserStatusByEmail($email, $hash)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."users WHERE email = '$email' AND status = 0 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)){

            $status = 1;

            $sql = 'UPDATE '.PREFICS.'users SET status = :status, pass = :pass WHERE user_id = '.$data['user_id'];
            $result = $db->prepare($sql);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':pass', $hash, PDO::PARAM_STR);
            $result->execute();

        }
        return $data;
    }



    // ОБНОВИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
    public static function UpdateUserSelf($id, $name, $phone, $zipcode, $city, $address, $surname, $nick_telegram, $nick_instagram, $sex, $day, $month, $year, $vk_url)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'users SET user_name = :user_name, phone = :phone, city = :city, address = :address, zipcode = :zipcode, 
        surname = :surname, nick_telegram = :nick_telegram, nick_instagram = :nick_instagram, sex = :sex, bith_day = :day, bith_month = :month, bith_year = :year, vk_url = :vk_url
                WHERE user_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':user_name', $name, PDO::PARAM_STR);
        $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':city', $city, PDO::PARAM_STR);
        $result->bindParam(':address', $address, PDO::PARAM_STR);
        $result->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);

        $result->bindParam(':surname', $surname, PDO::PARAM_STR);
        $result->bindParam(':nick_telegram', $nick_telegram, PDO::PARAM_STR);
        $result->bindParam(':nick_instagram', $nick_instagram, PDO::PARAM_STR);
        $result->bindParam(':sex', $sex, PDO::PARAM_STR);
        $result->bindParam(':vk_url', $vk_url, PDO::PARAM_STR);
        $result->bindParam(':day', $day, PDO::PARAM_INT);
        $result->bindParam(':month', $month, PDO::PARAM_INT);
        $result->bindParam(':year', $year, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    public static function updateNickTelegram($user_id, $nick_telegram) {
        $sql = 'UPDATE '.PREFICS."users SET nick_telegram = :nick_telegram WHERE user_id = :user_id";
        $db = Db::getConnection();
        $result = $db->prepare($sql);
        
        $result->bindParam(':nick_telegram', $nick_telegram, PDO::PARAM_STR);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    // ПОЛУЧАЕТ СПИСОК ПОЛЬЗОВАТЕЛЕЙ ДЛЯ АДМИНКИ (по ролям)
    public static function getUserListForAdmin($role, $page = 1, $show_items = 20, $user_group = null, $fields_map = [])
    {
        $offset = ($page - 1) * $show_items;
        $filters = [];

        if (!empty($fields_map)) {
            foreach ($fields_map as $fields_type => $fields) {
                if (!is_array($fields)) {
                    continue;
                }

                foreach ($fields as $field_name => $field_value) {
                    if ($field_name == 'name' || $field_name == 'id') {
                        $field_name = 'user_'.$field_name;
                    }

                    if ($field_name == 'login' || $field_name == 'pass' || $field_value === '') {
                        continue;
                    } elseif ($fields_type === 'text') {
                        $filters[] = "$field_name LIKE '%$field_value%'";
                    } elseif ($fields_type === 'numbers' && (int)$field_value !== 0) {
                        $filters[] = "$field_name IN (".implode(',', array_map('intval', explode(',', $field_value))).")";
                    }
                }
            }
        }

        if ($user_group === false) {
            $filters[] = "user_id NOT IN (SELECT user_id FROM ".PREFICS."user_groups_map GROUP BY user_id)";
        } elseif ($user_group) {
            $filters[] = "user_id IN (SELECT user_id FROM ".PREFICS."user_groups_map WHERE group_id = $user_group)";
        }

        if($role !== 0){
            if (in_array($role, array('admin', 'manager'))) {
                $filters[] = "role = '$role'";
            } else {
                $filters[] = "$role = 1";
            }
        }

        $filter = $filters ? 'WHERE '.implode(' AND ', $filters) : '';

        $query = "SELECT user_id, user_name, email, role, reg_date, status, phone, is_client FROM ".PREFICS."users $filter ORDER BY user_id DESC";

        if ($user_group == null && $user_group !== false) {
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


    // ПОЛУЧАЕТ КОЛ_ВО ОБЫЧНЫХ ЮЗЕРОВ
    public static function countRegUsers($start_date = 0, $client = 0)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."users WHERE is_client = $client AND reg_date > $start_date ");
        $count = $result->fetch();
        if($count[0] > 0) return $count[0];
        else return false;
    }



    // ПОЛУЧАЕТ СПИСОК АВТОРОВ
    public static function getAuthors()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT user_id, user_name FROM ".PREFICS."users WHERE is_author = 1");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['user_id'] = $row['user_id'];
            $data[$i]['user_name'] = $row['user_name'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК КУРАТОРОВ
     * @return array|bool
     */
    public static function getCurators()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT user_id, user_name, surname FROM ".PREFICS."users WHERE is_curator = 1");
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }



    // ПОЛУЧАЕТ ДАННЫЕ ПОЛЬЗОВТАЕЛЯ ПО ID
    public static function getUserDataForAdmin($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."users WHERE user_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }



    // ПОЛУЧАЕТ имя и емейл юзера по ID
    public static function getUserNameByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT user_id, user_name, email, status, sex, photo_url FROM ".PREFICS."users WHERE user_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    // ПОЛУЧАЕТ имена и фамилии юзеров по ID
    // используется для вывода авторов в списке тренингов в админке
    public static function getUserNameByListID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT user_name, surname FROM ".PREFICS."users WHERE user_id IN($id) ");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }


    // ПОЛУЧАЕТ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ПО reg_key
    public static function getUserDataToRegkey($reg_key)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."users WHERE reg_key = '$reg_key' AND status = 0");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }



    // ОБНОВЛЯЕТ СТАТУС ПОЛЬЗОВАТЕЛЯ
    public static function updateUserStatus($user_id, $status)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'users SET status = :status WHERE user_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    // ПОЛУЧАЕТ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ПО NICK_TELEGRAM
    public static function getUserDataToNickTelegram($nick, $status = null)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."users WHERE nick_telegram = :nick";
        $sql .= ($status !== null ? " AND status = $status" : '') . ' LIMIT 1';

        $result = $db->prepare($sql);
        $result->bindParam(':nick', $nick, PDO::PARAM_STR);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }


    // ПОЛУЧАЕТ КОЛИЧЕСТВО ПОЛЬЗОВАТАЕЛЕЙ ПО NICK_TELEGRAM
    public static function getCountUsersByNickTelegram($nick)
    {
        $db = Db::getConnection();
        $sql = "SELECT COUNT(user_id) FROM ".PREFICS."users WHERE nick_telegram = :nick";

        $result = $db->prepare($sql);
        $result->bindParam(':nick', $nick, PDO::PARAM_STR);
        $result->execute();
        $count = $result->fetch();

        return $count[0];
    }

    /**
     * ПОЛУЧАЕТ ДАННЫЕ ПОЛЬЗОВТАЕЛЯ ПО EMAIL
     * @param $email
     * @param int $status
     * @return bool|mixed
     */
    public static function getUserDataByEmail($email, $status = 1)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."users WHERE email = '$email'";
        $query .= ($status !== null ? ' AND status = 1' : '') . ' LIMIT 1';
        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }

    // ПОЛУЧАЕТ ID ПОЛЬЗОВАТЕЛЯ ПО ЕМЕЙЛ
    public static function getUserIDatEmail($email)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT user_id FROM ".PREFICS."users WHERE email = '$email' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['user_id'] : false;
    }



    // ОБНОВИТЬ СПЕЦ, РЕЖИМ ПАРТЁРКИ ЮЗЕРА
    public static function updateSpecUser($id, $params)
    {
        $db = Db::getConnection(); 
        $sql = 'UPDATE '.PREFICS.'users_spec_aff SET type = :type, float_scheme = :float_scheme, comiss = :comiss WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':type', $params['type'], PDO::PARAM_INT);
        $result->bindParam(':comiss', $params['comiss'], PDO::PARAM_INT);
        $result->bindParam(':float_scheme', $params['float'], PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ ПРОДУКТ ИЗ СПЕЦ РЕЖИМА
    public static function deleteSpecAff($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'users_spec_aff WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    // ПОЛУЧИТЬ ПРОДУКТЫ ДЛЯ СПЕЦ РЕЖИМА
    public static function getProductsForSpecAff($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."users_spec_aff WHERE user_id = $user_id");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['user_id'] = $row['user_id'];
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['type'] = $row['type'];
            $data[$i]['float_scheme'] = $row['float_scheme'];
            $data[$i]['comiss'] = $row['comiss'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ДОБАВИТЬ ПРОДУКТ В СПЕЦРЕЖИМ ДЛЯ ПАРТНЁРА
    public static function AddProductSpecAff($user_id, $params)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'users_spec_aff (user_id, product_id, type, float_scheme, comiss ) 
                VALUES (:user_id, :product_id, :type, :float_scheme, :comiss )';
        
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':product_id', $params['products'], PDO::PARAM_INT);
        $result->bindParam(':type', $params['type'], PDO::PARAM_INT);
        $result->bindParam(':float_scheme', $params['float'], PDO::PARAM_STR);
        $result->bindParam(':comiss', $params['comiss'], PDO::PARAM_INT);
        return $result->execute();
        
    }


    /**
     * ИЗМЕНИТЬ ЮЗЕРА
     * @param $user_id
     * @param $name
     * @param $email
     * @param $phone
     * @param $city
     * @param $zipcode
     * @param $address
     * @param $note
     * @param $status
     * @param $pass
     * @param $groups
     * @param $groups_dates
     * @param $is_partner
     * @param $is_subs
     * @param $role
     * @param $login
     * @param $surname
     * @param $sex
     * @param $nick_telegram
     * @param $nick_instagram
     * @param $level
     * @param string $vk_url
     * @param $spec_aff
     * @param $curators
     * @return bool
     */
    public static function editUser($user_id, $name, $email, $phone, $city, $zipcode, $address, $note, $status, $pass, $groups,
                                    $groups_dates, $is_partner, $is_subs, $role, $login, $surname, $sex, $nick_telegram,
                                    $nick_instagram, $level, $vk_url = '', $spec_aff, $curators)
    {
        $db = Db::getConnection();

        $sql = "UPDATE ".PREFICS."users SET user_name = :user_name, email = :email, phone = :phone, city = :city, address = :address,
                zipcode = :zipcode, note = :note, status = :status, is_partner = :is_partner, is_subs = :is_subs, role = :role,
                login = :login, surname = :surname, sex = :sex, nick_telegram = :nick_telegram, nick_instagram = :nick_instagram,
                vk_url = :vk_url, level = :level, spec_aff = :spec_aff";

        $hash = !empty($pass) ? password_hash($pass, PASSWORD_DEFAULT) : null;
        $sql .= ($hash ? ", pass = '$hash'" : '') . " WHERE user_id = :user_id";

        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':user_name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        
        $result->bindParam(':surname', $surname, PDO::PARAM_STR);
        $result->bindParam(':sex', $sex, PDO::PARAM_STR);
        $result->bindParam(':nick_telegram', $nick_telegram, PDO::PARAM_STR);
        $result->bindParam(':nick_instagram', $nick_instagram, PDO::PARAM_STR);
        $result->bindParam(':vk_url', $vk_url, PDO::PARAM_STR);

        $result->bindParam(':role', $role, PDO::PARAM_STR);
        $result->bindParam(':login', $login, PDO::PARAM_STR);
        
        $result->bindParam(':city', $city, PDO::PARAM_STR);
        $result->bindParam(':address', $address, PDO::PARAM_STR);
        $result->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
        $result->bindParam(':note', $note, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':is_partner', $is_partner, PDO::PARAM_INT);
        $result->bindParam(':is_subs', $is_subs, PDO::PARAM_INT);
        $result->bindParam(':level', $level, PDO::PARAM_INT);
        $result->bindParam(':spec_aff', $spec_aff, PDO::PARAM_INT);
        $res = $result->execute();
        
        // Записать группы пользователю, предварительно удалив их все (обновление групп))
        self::deleteUserGroups($user_id); // удалить все группы пользователя

        if ($groups) {
            foreach($groups as $key => $group) {
                $date = isset($groups_dates[$key]) && !empty($groups_dates[$key]) ? strtotime($groups_dates[$key]) : null;
                self::WriteUserGroup($user_id, $group, $date);
            }
        }

        
        return $res;
    }


    /**
     * УДАЛЕНИЕ ЮЗЕРА и УДАЛЕНИЕ ЕГО ГРУПП
     * @param $user_id
     * @return bool
     */
    public static function deleteUser($user_id)
    {
        if ($_SESSION['admin_user'] == $user_id) { // проверка на самоудаление
            System::redirectUrl("/admin/users", false);
        }

        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'users WHERE user_id = :user_id';
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();

        return self::deleteUserGroups($user_id); // удалить группы пользователя
    }


    // СМЕНА ПАРОЛЯ ЮЗЕРОМ
    public static function ChangePass($user_id, $pass)
    {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."users SET pass = :pass WHERE user_id = $user_id";
        $result = $db->prepare($sql);
        $result->bindParam(':pass', $hash, PDO::PARAM_STR);
        
        return $result->execute();
    }


    /**
     * КОЛ-ВО ЮЗЕРОВ
     * @param null $start_date
     * @param null $end_date
     * @param null $is_client
     * @return mixed
     */
    public static function countUsers($start_date = null, $end_date = null, $is_client = null)
    {
        $db = Db::getConnection();

        $clauses = [];
        if ($start_date) {
            $clauses[] = "reg_date >= $start_date";
        }
        if ($end_date) {
            $clauses[] = "reg_date <= $end_date";
        }
        if ($is_client) {
            $clauses[] = "is_client = 1";
        }

        $where = !empty($clauses) ? ('WHERE ' . implode(' AND ', $clauses)) : '';
        $query = "SELECT COUNT(user_id) FROM ".PREFICS."users $where";
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0];
    }



    // ПОЛУЧИТЬ СПИСОК ВХОЖИХ В АДМИНКУ
    public static function getAdministrationUser()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT user_id, user_name FROM ".PREFICS."users WHERE role IN ( 'admin', 'manager')");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /*---------- USER GROUPS ---------- */


    /**
     * ПОЛУЧИТЬ ЮЗЕРОВ ОПРЕДЕЛЁННОЙ ГРУППЫ
     * @param $group_id
     * @return bool
     */
    public static function getUsersFromGroup($group_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT user_id, date FROM ".PREFICS."user_groups_map WHERE group_id = $group_id ORDER BY user_id DESC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // ПОЛУЧИТЬ СПИСОК ПОЛЬЗОВАТЕЛЬСКИХ ГРУПП
    public static function getUserGroups()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT group_id, group_name, group_title FROM ".PREFICS."user_groups ORDER BY group_title ASC");

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ НОВУЮ ГРУПУ В СИСТЕМУ
     * @param $name
     * @param $title
     * @param $desc
     * @param $del_tg_chats
     * @return bool
     */
    public static function AddNewUserGroup($name, $title, $desc, $del_tg_chats)
    {
        $db = Db::getConnection();
        $date = time();
        $sql = 'INSERT INTO '.PREFICS.'user_groups (group_name, group_title, group_desc, create_date, del_tg_chats)
                VALUES (:name, :group_title, :group_desc, :create_date, :del_tg_chats)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':group_title', $title, PDO::PARAM_STR);
        $result->bindParam(':group_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':create_date', $date, PDO::PARAM_INT);
        $result->bindParam(':del_tg_chats', $del_tg_chats, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ГРУППЫ ПО ID ГРУППЫ
     * @param $group_id
     * @return bool|mixed
     */
    public static function getUserGroupData($group_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."user_groups WHERE group_id = $group_id");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * РЕДАКТИРОВАТЬ ДАННЫЕ ГРУППЫ
     * @param $id
     * @param $name
     * @param $title
     * @param $desc
     * @param $del_tg_chats
     * @return bool
     */
    public static function EditUserGroup($id, $name, $title, $desc, $del_tg_chats)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."user_groups SET group_name = :name, group_title = :group_title,
                group_desc = :group_desc, del_tg_chats = :del_tg_chats WHERE group_id = $id";

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':group_title', $title, PDO::PARAM_STR);
        $result->bindParam(':group_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':del_tg_chats', $del_tg_chats, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ГРУППУ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @param $group_id
     * @return bool
     */
    public static function deleteUserGroup($user_id, $group_id) {
        if (Telegram::getStatus()) { // Удаление пользователя из telegram
            Telegram::delUserFromChatsToGroup($user_id, $group_id);
        }

        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'user_groups_map WHERE user_id = :user_id AND group_id = :group_id';
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':group_id', $group_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ГРУППЫ ПОЛЬЗОВАТЕЛЯ ИЗ СПИСКА
     * @param $user_id
     * @param $group_ids
     * @return bool
     */
    public static function deleteUserGroupsFromList($user_id, $group_ids) {
        $groups = is_array($group_ids) ? $group_ids : explode(',', $group_ids);
        $results = [];

        if ($groups) {
            foreach ($groups as $group_id) {
                $results[] = self::deleteUserGroup($user_id, (int)$group_id);
            }
        }

        return empty($results) || in_array(false, $results) ? false : true;
    }


    /**
     * УДАЛИТЬ ГРУППЫ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @return bool
     */
    public static function deleteUserGroups($user_id) {
        if (Telegram::getStatus()) { // Удаление пользователя из telegram
            Telegram::delUserFromChatsToGroups($user_id);
        }

        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'user_groups_map WHERE user_id = :user_id';
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    // УДАЛИТЬ ГРУППУ и её записи в БД group_map
    public static function deleteGroup($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'user_groups WHERE group_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();

        $sql = 'DELETE FROM '.PREFICS.'user_groups_map WHERE group_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }



    // ПОЛУЧИТЬ ГРУППЫ ОДНОГО ПОЛЬЗОВАТЕЛЯ
    public static function getGroupByUser($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT group_id FROM ".PREFICS."user_groups_map WHERE user_id = $id ORDER BY group_id ASC");
    
        $data = [];
        while($row = $result->fetch()){
            $data[] = $row['group_id'];
        }
    
        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ГРУППЫ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @param $group_id
     * @return bool|mixed
     */
    public static function getGroupByUserAndGroup($user_id, $group_id)
    {
        $db = Db::getConnection();
        $result = $db->prepare("SELECT * FROM ".PREFICS."user_groups_map WHERE user_id = :user_id AND group_id = :group_id");

        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * СОХРАНИТЬ ПОЛЬЗОВАТАЕЛЮ ГРУППУ
     * @param $user_id
     * @param $group_id
     * @param null $date
     * @return bool
     */
    public static function WriteUserGroup($user_id, $group_id, $date = null)
    {
        $db = Db::getConnection();
        $date = $date ?: time();

        $result = $db->query("SELECT COUNT(group_id) FROM ".PREFICS."user_groups_map WHERE group_id = $group_id AND user_id = $user_id"); // ПРОВЕРИТЬ СУЩЕСТВОВАНИЕ ГРУППЫ И КЛИЕНТА
        $count = $result->fetch();

        if ($count[0] == 0) { // если нет группы
            $result = $db->prepare('INSERT INTO '.PREFICS.'user_groups_map (group_id, user_id, date ) VALUES (:group_id, :user_id, :date)');
            $result->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
			$result->bindParam(':date', $date, PDO::PARAM_INT);

			$result = $result->execute();

			if ($result && Telegram::getStatus()) { // Удаление пользователя из чс telegram
                Telegram::delUserFromBlacklistToGroup($user_id, $group_id);
            }

			return $result;
        }

        return false;
    }

     /**
     * ЗАПИСАТЬ ПОЛЬЗОВТАЕЛЮ КУРАТОРОВ в таблицу curator_to_user
     * @param $user_id
     * @param $curator_id
     * @param $training_id
     * @param $section_id
     * @return bool
     */
     public static function WriteCuratorsToUser($user_id, $curator_id, $training_id, $section_id = 0)
     {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(*) as count_record FROM ".PREFICS."training_curator_to_user
                                       WHERE user_id = $user_id AND curator_id = $curator_id
                                       AND training_id = $training_id  AND section_id = $section_id"
        );

        $count = $result->fetch();
        if ($count[0] != 0) {
            return true;
        }

        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training_curator_to_user (curator_id, user_id, training_id, section_id) 
                VALUES (:curator_id, :user_id, :training_id, :section_id)';
 
        $result = $db->prepare($sql);
        $result->bindParam(':curator_id', $curator_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':training_id', $training_id, PDO::PARAM_INT);
        $result->bindParam(':section_id', $section_id, PDO::PARAM_INT);

        return $result->execute();
     }



    /**
     *  АВТОРИЗАЦИЯ ЮЗЕРОВ
     */


    // ПРОВЕРКА АВТОРИЗОВАН ЛИ ЮЗЕР
    public static function checkLogged()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            header ("Location: /login");
            exit();
        }
    }


    // ПРОВЕРКА ПОЛЬЗОВАТЕЛЬ ГОСТЬ ИЛИ НЕТ
    public static function isAuth()
    {
        if(isset($_SESSION['user'])) return $_SESSION['user']; // Если авторизован, то вернёт ID юзера
        else return false;
    }


    // ПРОВЕРКА ДАННЫХ ПРИ АВТОРИЗАЦИИ
    public static function checkUserData($email, $pass)
    {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM '.PREFICS.'users WHERE email = :email AND status = 1';
        $result = $db->prepare($sql);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->execute();
        $user = $result->fetch();

        if($user) {
            $hash = $user['pass'];
            if(password_verify($pass, $hash)) return $user;
            else return false;
        }
        return false;
    }



    // АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ
    public static function Auth($id, $name)
    {
        $_SESSION['user'] = $id;
        $_SESSION['name'] = $name;

        $time = time();
        $_SESSION['user_token'] = md5($id.$time);

        // Записать в базу время последнего входа
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'users SET last_visit = :last_visit WHERE user_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':last_visit', $time, PDO::PARAM_INT);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }


    // ДАННЫЕ ЮЗЕРА ПО ID 
    public static function getUserById($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."users WHERE user_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }



    // ПОИСК ЕМЕЙЛ В ЧЁРНОМ СПИСКЕ
    public static function searchEmailinBL($email)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."user_blacklist WHERE email = '$email'");
        $count = $result->fetch();
        return $count[0];
    }


    // ДОБАВИТЬ ЕМЕЙЛ в ЧЁРНЫЙ СПИСОК
    public static function addBlackList($email, $act)
    {
        $db = Db::getConnection();
        if($act == 1){

            $check = self::searchEmailinBL($email);
            if($check == 0){
                $sql = 'INSERT INTO '.PREFICS.'user_blacklist (email ) 
                        VALUES (:email)';

                $result = $db->prepare($sql);
                $result->bindParam(':email', $email, PDO::PARAM_STR);
                return $result->execute();
            }

        } else {

            $sql = 'DELETE FROM '.PREFICS.'user_blacklist WHERE email = :email';
            $result = $db->prepare($sql);
            $result->bindParam(':email', $email, PDO::PARAM_STR);
            return $result->execute();
        }
    }


    /**
     * @param $user_id
     * @param $phone
     * @return bool
     */
    public static function confirmPhone($user_id, $phone) {
        $sql = 'UPDATE '.PREFICS."users SET phone = :phone, confirm_phone = :phone WHERE user_id = :user_id";
        $db = Db::getConnection();
        $result = $db->prepare($sql);

        $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':confirm_phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $user
     * @param $settings
     * @return string
     */
    public static function getAvatarUrl($user, $settings) {
        // Здесь в базе хранится полный урл картинки, потому-что из ВК аватарка грузится по полному пути с их сервера, что бы не ломать
        // совместимость тоже пишем
        if (!empty($user['photo_url'])) {
            $url = $user['photo_url'];
        } else {

            switch ($user['sex']) {
                case 'female':
                    $img_name = 'female_avatar.png';
                    break;
                case 'male':
                    $img_name = 'noavatar.png';
                    break;
                default:
                    $img_name = 'noavatar.png';
                    break;
            }
    
            $url = 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($user['email']))).'?s=130&d=';
            $url.= urlencode("{$settings['script_url']}/images/$img_name}");
        }
        
        return $url;
    }


    /**
     * ПОЛУЧИТЬ ДОЛЖНОСТЬ ПОЛЬЗОВАТЕЛЯ
     * @param $role
     * @return string
     */
    public static function getRoleUser($role) {
        $text = '';
        switch($role){
            case 'user':
                $text = 'Пользователь';
                break;

            case 'admin':
                $text = 'Администратор';
                break;

            case 'manager':
                $text = 'Менеджер';
                break;
        }

        return $text;
    }
}