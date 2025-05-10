<?php defined('BILLINGMASTER') or die;

class Telegram {

    use ResultMessage;

    const EVENT_DEL_USER_FROM_CHAT = 1;
    const EVENT_DEL_USER_FROM_CHAT_TO_DEL_GROUP = 2; // СОБЫТИЕ ПРИ УДАЛЕНИИ ГРУППЫ
    const EVENT_DEL_USER_FROM_CHAT_TO_DEL_GROUPS = 3; // СОБЫТИЕ ПРИ УДАЛЕНИИ ГРУПП (ПРИ УДАЛЕНИИ ПОЛЬЗОВАТЕЛЯ ИЛИ РЕДАКТИРОВАНИИ ЕГО ГРУПП)
    const EVENT_DEL_USER_FROM_CHAT_TO_DEL_SUBS = 4; // СОБЫТИЕ ПРИ УДАЛЕНИИ ПОДПИСКИ
    const EVENT_DEL_USER_FROM_CHAT_TO_DEL_HARES = 5; // СОБЫТИЕ ПРИ УДАЛЕНИИ ЗАЙЦЕВ ИЗ АДМИНКИ
    const EVENT_DEL_USER_FROM_BLACKLIST = 6; // СОБЫТИЕ ПРИ УДАЛЕНИИ ПОЛЬЗОВАТЕЛЯ ИЗ ЧС TG

    private static $api = null;


    /**
     * ПОЛУЧИТЬ API TELEGRAM
     * @return bool|TelegramApi|null
     */
    private static function getApi() {
        if (self::$api === null) {
            $settings = Telegram::getSettings();
            $params = unserialize($settings);
            if ($params['params']['token']) {
                self::$api = new TelegramApi($params['params']['token']);
            } else {
                self::$api = false;
            }
        }

        return self::$api;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС
     * @return mixed
     */
    public static function getStatus()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT enable FROM ".PREFICS."extensions WHERE name = 'telegram'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['enable'] : false;
    }


    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ
     * @return mixed
     */
    public static function getSettings()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT params FROM ".PREFICS."extensions WHERE name = 'telegram'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['params'] : false;
    }
    
    
    /**
     * ПОЛУЧИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ПО ПОЛЮ USERNAME
     * @param $username
     * @return bool
     */
    public static function getUserByUsername($username) {
        $db = Db::getConnection();

        $result = $db->prepare("SELECT * FROM ".PREFICS."telegram_users WHERE username = :username");
        $result->bindParam(':username', $username, PDO::PARAM_STR);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ПО ПОЛЮ HASH
     * @param $hash
     * @return bool|mixed
     */
    public static function getUserByHash($hash) {
        $db = Db::getConnection();

        $result = $db->prepare("SELECT * FROM ".PREFICS."telegram_users WHERE hash = :hash");
        $result->bindParam(':hash', $hash, PDO::PARAM_STR);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ПО ПОЛЮ SM_USER_ID
     * @param $sm_user_id
     * @return bool|mixed
     */
    public static function getUserBySmUserId($sm_user_id) {
        $db = Db::getConnection();

        $result = $db->prepare("SELECT * FROM ".PREFICS."telegram_users WHERE sm_user_id = :sm_user_id");
        $result->bindParam(':sm_user_id', $sm_user_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ПО ПОЛЮ USER_ID
     * @param $user_id
     * @return bool|mixed
     */
    public static function getUserByUserId($user_id) {
        $db = Db::getConnection();

        $result = $db->prepare("SELECT * FROM ".PREFICS."telegram_users WHERE user_id = :user_id");
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }

    /**
     * ПОЛУЧИТЬ ВСЕХ ПОЛЬЗОВАТЕЛЕЙ
     * @param bool $offset
     * @param bool $limit
     * @return array|bool
     */
    public static function getUsers($offset = false, $limit = false) {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."telegram_users";
        $query .= $limit ? " LIMIT $limit" : '';
        $query .= $offset ? " OFFSET $offset" : '';
        $result = $db->query($query);
        
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ВСЕХ ПОЛЬЗОВАТЕЛЕЙ
     * @return mixed
     */
    public static function getTotalUsers()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(sm_user_id) FROM " . PREFICS . "telegram_users");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * СОХРАНИТЬ НАСТРОЙКИ
     * @param $params
     * @param $status
     * @return bool
     */
    public static function saveSettings($params, $status = null) {
        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."extensions SET params = :params";
        $sql .= ($status !== null ? ', enable = '.intval($status) : '')." WHERE name = 'telegram'";
        
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * СОХРАНИТЬ ПОЛЬЗОВАТЕЛЯ
     * @param $sm_user_id
     * @param $user_name
     * @param $hash
     * @return bool
     */
    public static function saveUser($sm_user_id, $user_name, $hash) {
        $db = Db::getConnection();

        $sql = "REPLACE INTO ".PREFICS."telegram_users (sm_user_id, user_name, hash)
                VALUES(:sm_user_id, :user_name, :hash)";
        
        $result = $db->prepare($sql);
        $result->bindParam(':sm_user_id', $sm_user_id, PDO::PARAM_INT);
        $result->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $result->bindParam(':hash', $hash, PDO::PARAM_STR);
        
        return $result->execute();
    }


    /**
     * ОБНОВИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @param $user_name
     * @param $first_name
     * @param $last_name
     * @param $hash
     * @return bool
     */
    public static function updateUser($user_id, $user_name, $first_name, $last_name, $hash) {
        $db = Db::getConnection();

        $sql = "UPDATE ".PREFICS."telegram_users SET user_id = :user_id, user_name = :user_name, first_name = :first_name,
                last_name = :last_name WHERE hash = :hash";

        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $result->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $result->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $result->bindParam(':hash', $hash, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * СОХРАНИТЬ ПОЛЬЗОВАТЕЛЯ
     * @param $user_id
     * @param $user_name
     * @param $first_name
     * @param $last_name
     * @return bool
     */
    public static function addUnregisteredUser($user_id, $user_name, $first_name, $last_name) {
        $db = Db::getConnection();

        $sql = "INSERT INTO ".PREFICS."telegram_users (sm_user_id, user_id, user_name, first_name, last_name)
                VALUES(0, :user_id, :user_name, :first_name, :last_name)";

        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $result->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $result->bindParam(':last_name', $last_name, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ВЫВОДИТЬ ИЛИ НЕТ КНОПКУ ДЛЯ ПОДТВЕРЖДЕНИЯ АККАУНТА В TG
     * @param $sm_user_id
     * @param $nick_telegram
     * @return bool
     */
    public static function isShowButton($sm_user_id, $nick_telegram) {
        $tg_enabled = System::CheckExtensension('telegram', 1);
        if (!$tg_enabled) {
            return false;
        }

        if (self::checkBindingUser($sm_user_id, $nick_telegram)) {
            return false;
        }

        $have_groups = self::searchGroupsToActs($sm_user_id);
        if (!$have_groups) {
            return false;
        }

        return true;
    }


    /**
     * ПОЛУЧИТЬ ССЫЛКУ ДЛЯ ПОДТВЕРЖДЕНИЯ АККАУНТА В TG
     * @param $sm_user_id
     * @param $nick_telegram
     * @return bool|string
     */
    public static function getLinkToBindAccount($sm_user_id, $nick_telegram) {
        if (!self::isShowButton($sm_user_id, $nick_telegram)) {
            return false;
        }

        $settings = self::getSettings();
        $params = unserialize($settings);
        
        if ($params['params']['bot_name']) {
            return "https://t.me/{$params['params']['bot_name']}?start=".md5(System::generateStr(8) . microtime(1));
        }
    
        return false;
    }
    
    
    /**
     * ПОИСК ГРУПП ДЛЯ КОТОРЫХ ДЕЙСТВУЕТ РАСШИРЕНИЕ
     * @param $sm_user_id
     * @return bool
     */
    public static function searchGroupsToActs($sm_user_id) {
        $settings = Telegram::getSettings();
        $params = unserialize($settings);
        $tg_user_groups = !empty($params['params']['tg_user_groups']) ? implode(',', $params['params']['tg_user_groups']) : null;

        if (!$tg_user_groups) {
            return false;
        }

        $db = Db::getConnection();
        $sql = 'SELECT COUNT(id) FROM '.PREFICS."user_groups_map WHERE user_id = $sm_user_id AND group_id IN ($tg_user_groups)";

        $result = $db->query($sql);
        $count = $result->fetch();
        
        return $count[0] > 0 ? $count[0] : false;
    }


    /**
     * ПРОВЕРИТЬ ПРИВЯЗАН ЛИ ПОЛЬЗОВАТЕЛЬ К ТЕЛЕГРАММУ
     * @param $sm_user_id
     * @param null $nick
     * @return bool
     */
    public static function checkBindingUser($sm_user_id, $nick = null) {
        $tg_user = self::getUserBySmUserId($sm_user_id);
        if ($tg_user['user_id']) {
            return true;
        }

        return false;
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧАТА ПО ГРУППЕ
     * @param $sm_user_id
     * @param $group_id
     * @return bool|mixed
     */
    public static function delUserFromChatsToGroup($sm_user_id, $group_id) {
        $group = User::getUserGroupData($group_id);
        if ($group && $group['del_tg_chats']) {
            return self::delUserFromChats($sm_user_id, $group['del_tg_chats'], false, self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_GROUP, $group_id);
        }

        return false;
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧАТА ДЛЯ ГРУПП (ВЫЗЫАЕТСЯ ПРИ УДАЛЕНИИ ГРУПП В АДМИНКЕ: ПРИ УДАЛЕНИИ ПОЛЬЗОВАТЕЛИ ИЛИ РЕДАКТИРОВАНИИ ГРУПП ПОЛЬЗОВАТЕЛЯ)
     * @param $sm_user_id
     * @param $groups
     * @return bool|mixed
     */
    public static function delUserFromChatsToGroups($sm_user_id, $groups = null) {
        if ($groups === null) {
            $groups = User::getGroupByUser($sm_user_id);
        }

        if ($groups) {
            foreach ($groups as $group_id) {
                $group = User::getUserGroupData($group_id);
                if ($group && $group['del_tg_chats']) {
                    self::delUserFromChats($sm_user_id, $group['del_tg_chats'], false, self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_GROUPS, $group_id);
                }
            }

        }
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧАТА ДЛЯ ПОДПИСКИ, ЕСЛИ НЕТ ДЕЙСТВУЮЩЕЙ ПОДПИСКИ
     * @param $sm_user_id
     * @param $del_chats
     * @param $subs_id
     * @return bool|mixed
     */
    public static function delUserFromChatsToSub($sm_user_id, $del_chats, $subs_id) {
        $tg_user = Telegram::getUserBySmUserId($sm_user_id);
        if (!$tg_user['user_id']) {
            return false;
        }

        $api = self::getApi();
        if (!$api) {
            return false;
        }

        $del_chats = explode(',', $del_chats);
        foreach ($del_chats as $del_chat) {
            $count = Member::countActiveSubsWithTgChat2User($del_chat, $sm_user_id); // получить количество действующих подписок для пользователя с этим чатом
            if ($count == 0) {
                $res = self::delUserFromChat($api, $tg_user['user_id'], $del_chat, self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_SUBS, $subs_id);
            }
        }
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧАТОВ
     * @param $sm_user_id
     * @param $del_chats
     * @param bool $use_check проверять, евляется ли пользователь членом группы/чаты
     * @param $event_type
     * @param $event_value
     * @return bool
     */
    public static function delUserFromChats($sm_user_id, $del_chats, $use_check = false, $event_type, $event_value = 0) {
        $api = self::getApi();
        if (!$api) {
            return false;
        }

        $result = false;
        $tg_user = Telegram::getUserBySmUserId($sm_user_id);
        if ($tg_user['user_id']) {
            foreach (explode(',', $del_chats) as $del_chat) {
                $del_chat = trim($del_chat);
                if (!$use_check || $api->isMember($tg_user['user_id'], $del_chat)) {
                    $res = $api->removeMember($tg_user['user_id'], $del_chat);
                    if ($res) {
                        self::writeLog($event_type, $event_value, $sm_user_id, $tg_user['user_id'], $del_chat);
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧАТА
     * @param $api
     * @param $user_id
     * @param $del_chat
     * @param $event
     * @param $event_value
     * @return bool
     */
    public static function delUserFromChat($api, $user_id, $del_chat, $event, $event_value = 0) {
        $res = $api->removeMember($user_id, $del_chat);
        if ($res) {
            self::writeLog($event, $event_value, 0, $user_id, $del_chat);
            return true;
        }

        return false;
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧС ДЛЯ ГРУППЫ
     * @param $sm_user_id
     * @param $group_id
     * @return bool|void
     */
    public static function delUserFromBlacklistToGroup($sm_user_id, $group_id) {
        $group = User::getUserGroupData($group_id);
        if ($group && $group['del_tg_chats']) {
            return self::delUserFromBlacklist($sm_user_id, $group['del_tg_chats']);
        }

        return false;
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ ЧС
     * @param $sm_user_id
     * @param $tg_chats
     * @return bool
     */
    public static function delUserFromBlacklist($sm_user_id, $tg_chats) {
        $api = self::getApi();
        if (!$api) {
            return false;
        }

        $result = false;
        $tg_user = Telegram::getUserBySmUserId($sm_user_id);
        if ($tg_user['user_id']) {
            foreach (explode(',', $tg_chats) as $tg_chat) {
                $tg_chat = trim($tg_chat);
                $res = $api->removeMemberFromBlacklist($tg_user['user_id'], $tg_chat);
                if ($res) {
                    self::writeLog(self::EVENT_DEL_USER_FROM_BLACKLIST, 0, $sm_user_id, $tg_user['user_id'], $tg_chat);
                    $result = true;
                }
            }
        }

        return $result;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ЛОГОВ
     * @param $filter
     * @return mixed
     */
    public static function getTotalLog($filter) {
        $clauses = [];
        if ($filter) {
            if ($filter['email']) {
                $clauses[] = "u.email LIKE '%{$filter['email']}%'";
            }
            if ($filter['username']) {
                $clauses[] = "tu.user_name = '{$filter['username']}'";
            }
            if ($filter['sm_user_id']) {
                $clauses[] = "tl.sm_user_id = {$filter['sm_user_id']}";
            }
            if ($filter['chat_id']) {
                $clauses[] = "tl.chat_id = '{$filter['chat_id']}'";
            }
            if ($filter['start']) {
                $clauses[] = "tl.date >= {$filter['start']}";
            }
            if ($filter['finish']) {
                $clauses[] = "tl.date < {$filter['finish']}";
            }
        }

        $where = !empty($clauses) ? (' WHERE ' . implode(" AND ", $clauses)) : '';

        $db = Db::getConnection();
        $query = "SELECT COUNT(tl.log_id) FROM ".PREFICS."telegram_log AS tl
                  INNER JOIN ".PREFICS."telegram_users AS tu ON tu.sm_user_id = tl.sm_user_id
                  INNER JOIN ".PREFICS."users AS u ON u.user_id = tl.sm_user_id $where";
        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * ПОЛУЧИТЬ СПИСОК ЛОГОВ
     * @param $filter
     * @param $page
     * @param $show_items
     * @return array|bool
     */
    public static function getLogList($filter, $page, $show_items) {
        $clauses = [];
        if ($filter) {
            if ($filter['email']) {
                $clauses[] = "u.email LIKE '%{$filter['email']}%'";
            }
            if ($filter['username']) {
                $clauses[] = "tu.user_name = '{$filter['username']}'";
            }
            if ($filter['sm_user_id']) {
                $clauses[] = "tl.sm_user_id = {$filter['sm_user_id']}";
            }
            if ($filter['chat_id']) {
                $clauses[] = "tl.chat_id = '{$filter['chat_id']}'";
            }
            if ($filter['start']) {
                $clauses[] = "tl.date >= {$filter['start']}";
            }
            if ($filter['finish']) {
                $clauses[] = "tl.date < {$filter['finish']}";
            }
        }

        $where = !empty($clauses) ? (' WHERE ' . implode(" AND ", $clauses)) : '';

        $offset = ($page - 1) * $show_items;
        $query = "SELECT tl.* FROM ".PREFICS."telegram_log AS tl
                  INNER JOIN ".PREFICS."telegram_users AS tu ON tu.sm_user_id = tl.sm_user_id
                  INNER JOIN ".PREFICS."users AS u ON u.user_id = tl.sm_user_id $where
                  GROUP BY tl.log_id ORDER BY date DESC LIMIT $show_items OFFSET $offset";

        $db = Db::getConnection();
        $result = $db->query($query);

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ СООБЩЕНИЕ СОБЫТИЯ
     * @param $log
     * @return string
     */
    public static function getMessageToEvent($log) {
        switch ($log['event_type']) {
            case self::EVENT_DEL_USER_FROM_CHAT: //
                return "Пользователь удален из чата {$log['chat_id']}";
            case self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_GROUP: // ПРИ УДАЛЕНИИ ГРУППЫ
                return "Пользователь удален из чата {$log['chat_id']} при удалении <a href=\"/admin/usergroups/edit/{$log['event_value']}\" target=\"_blank\">группы с id: {$log['event_value']}</a>";
            case self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_GROUPS: // ПРИ УДАЛЕНИИ ГРУПП (ПРИ УДАЛЕНИИ ПОЛЬЗОВАТЕЛЯ ИЛИ РЕДАКТИРОВАНИИ ЕГО ГРУПП)
                return "Пользователь удален из чата {$log['chat_id']} при удалении <a href=\"/admin/usergroups/edit/{$log['event_value']}\" target=\"_blank\">группы с id: {$log['event_value']}</a> (при удалении пользователя или редактировании групп из админки)";
            case self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_SUBS: // ПРИ УДАЛЕНИИ ПОДПИСКИ
                return "Пользователь удален из чата {$log['chat_id']} при удалении <a href=\"/admin/membersubs/edit/{$log['event_value']}\" target=\"_blank\">подписки с id: {$log['event_value']}</a>";
            case self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_HARES: // ПРИ УДАЛЕНИИ ЗАЙЦЕВ ИЗ АДМИНКИ
                return "Пользователь удален из чата {$log['chat_id']} при удалении зайцев {$log['event_value']}";
            case self::EVENT_DEL_USER_FROM_BLACKLIST: // ПРИ УДАЛЕНИИ ПОЛЬЗОВАТЕЛЯ ИЗ ЧС TG
                return "Пользователь удален из чс для чата {$log['chat_id']}";
        }
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ СПИСКА УЧАСТНИКОВ
     * @param $sm_user_id
     * @return bool
     */
    public static function delMember($sm_user_id) {
        $db = Db::getConnection();
        $sql = "DELETE FROM ".PREFICS."telegram_users WHERE sm_user_id = :sm_user_id";

        $result = $db->prepare($sql);
        $result->bindParam(':sm_user_id', $sm_user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * УДАЛИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ СПИСКА УЧАСТНИКОВ ПО ПОЛЯМ USER_ID И SM_USER_ID
     * @param $user_id
     * @param $sm_user_id
     * @return bool
     */
    public static function delMemberByUserId($user_id, $sm_user_id) {
        $db = Db::getConnection();
        $sql = "DELETE FROM ".PREFICS."telegram_users WHERE sm_user_id = :sm_user_id AND user_id = :user_id";
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':sm_user_id', $sm_user_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ СПИСОК ЧАТОВ ДЛЯ УДАЛЕНИЯ ИЗ НИЗ ПОЛЬЗОВАТАЛЕЙ
     * @param $ids
     * @return bool
     */
    public static function getGroupsChats($ids = []) {
        $db = Db::getConnection();
        $where = "WHERE del_tg_chats <> '' AND del_tg_chats IS NOT NULL" . ($ids ? ' AND group_id IN ('.implode(',', $ids).')' : '');

        $result = $db->query("SELECT GROUP_CONCAT(DISTINCT del_tg_chats) FROM ".PREFICS."user_groups $where");
        $data = $result->fetch();

        return !empty($data) ? $data[0] : false;
    }


    /**
     * ПОЛУЧИТЬ СПИСОК ПЛАНОВ ПОДПИСОК ДЛЯ УДАЛЕНИЯ ИЗ НИЗ ПОЛЬЗОВАТАЛЕЙ
     * @param array $ids
     * @return bool
     */
    public static function getPlanesChats($ids = []) {
        $db = Db::getConnection();
        $where = "WHERE del_tg_chats <> '' AND del_tg_chats IS NOT NULL AND status = 1" . ($ids ? ' AND id IN ('.implode(',', $ids).')' : '');
        $result = $db->query("SELECT GROUP_CONCAT(DISTINCT del_tg_chats) FROM ".PREFICS."member_planes $where");
        $data = $result->fetch();

        return !empty($data) ? $data[0] : false;
    }


    /**
     * УДАЛИТЬ ЗАЙЦЕВ ИЗ КАНАЛОВ
     * @param $users
     * @return bool|int
     */
    public static function delStowaways($users) {
        $all_chats = self::getChats();
        $del_users = 0;

        if ($all_chats && $users) {
            foreach ($users as $key => $user) {
                $delete = false;
                $users_chats = self::getChats($user['sm_user_id']);
                $diff_chats = array_diff($all_chats, $users_chats);
                $del_chats = empty($diff_chats) ? $all_chats : $diff_chats;
                if ($del_chats) {
                    foreach ($del_chats as $del_chat) {
                        if (self::delUserFromChats($user['sm_user_id'], $del_chat, true, self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_HARES)) {
                            $delete = true;
                        }
                    }
                    if ($delete) {
                        $del_users++;
                    }
                }
            }
        }

        return $del_users;
    }


    /**
     * УДАЛИТЬ ЗАЙЦА ИЗ КАНАЛОВ
     * @param $user_id
     * @param $chats
     * @return int
     */
    public static function delStowaway2Chanel($user_id, $chats) {
        $api = self::getApi();
        if (!$api) {
            return false;
        }

        $is_del = false;
        foreach ($chats as $chat_id) {
            if (self::delUserFromChat($api, $user_id, $chat_id, self::EVENT_DEL_USER_FROM_CHAT_TO_DEL_HARES)) {
                $is_del = true;
            }
        }

        return $is_del;
    }


    public static function getChats($sm_user_id = null) {
        if ($sm_user_id) {
            $user_groups = User::getGroupByUser($sm_user_id);
            $user_planes = Member::getPlanesByUser($sm_user_id, 1, true);
            $groups_chats = $user_groups ? self::getGroupsChats($user_groups) : null;
            $planes_chats = $user_planes ? self::getPlanesChats($user_planes) : null;
        } else {
            $groups_chats = self::getGroupsChats();
            $planes_chats = self::getPlanesChats();
        }

        if (!$groups_chats && !$planes_chats) {
            return [];
        }

        return self::getUniqueTGChats($groups_chats, $planes_chats);
    }


    /**
     * ПОЛУЧИТЬ УНИКАЛЬНЫЕ ЧАТЫ TG
     * @param $group_chats
     * @param $planes_chats
     * @return array|mixed|string
     */
    private static function getUniqueTGChats($group_chats, $planes_chats) {
        if (!$group_chats && $planes_chats) {
            $tg_chats = $planes_chats;
        } elseif ($group_chats && $planes_chats) {
            $tg_chats = "$group_chats,$planes_chats";
        } else {
            $tg_chats = $group_chats ? $group_chats : $planes_chats;
        }

        $tg_chats = str_replace(' ', '', $tg_chats);
        $tg_chats = array_unique(explode(',', $tg_chats));

        return $tg_chats;
    }


    /**
     * ПРИВЯЗАТЬ ПОЛЬЗОВАТЕЛЯ
     * @param $api
     * @param $message
     * @return bool
     */
    public static function bindUser($api, $message) {
        $from = $message['from'];
        $text = $message['text'];
        $chat = $message['chat'];
        $is_bot = $from['is_bot'];
        if ($is_bot) {
            return false;
        }

        $user_id = $from['id'];
        $user_name = $from['username'];
        $first_name = $from['first_name'];
        $last_name = $from['last_name'];
        $chat_id = $chat['id'];

        $hash = trim(str_replace('/start ', '', $text));
        $tg_user = $hash ? Telegram::getUserByHash($hash) : null;
        if (!$tg_user) {
            return false;
        }

        $res = Telegram::updateUser($user_id, $user_name, $first_name, $last_name, $hash);
        if ($res && $chat_id && $user_id) {
            $api->sendMessage($chat_id, 'Ваш аккаунт успешно привязан');
            return $user_id;
        } elseif(!$res) {
            $api->sendMessage($chat_id, 'Не удалось привязать ваш аккаунт');
            return false;
        }

        return false;
    }


    /**
     * ЗАПИСАТЬ ЛОГ
     * @param $event_type
     * @param $event_value
     * @param $sm_user_id
     * @param $user_id
     * @param $chat_id
     * @return bool
     */
    public static function writeLog($event_type, $event_value, $sm_user_id, $user_id, $chat_id) {
        $db = Db::getConnection();
        $date = time();
        $sql = "INSERT INTO ".PREFICS."telegram_log (event_type, event_value, sm_user_id, user_id, chat_id, date)
                VALUES(:event_type, :event_value, :sm_user_id, :user_id, :chat_id, '$date')";

        $result = $db->prepare($sql);
        $result->bindParam(':event_type', $event_type, PDO::PARAM_INT);
        $result->bindParam(':event_value', $event_value, PDO::PARAM_INT);
        $result->bindParam(':sm_user_id', $sm_user_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * УДАЛЕНИЕ СТАРЫХ ЛОГОВ
     * @param $date
     * @return bool
     */
    public static function delOldLogs($date) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'telegram_log WHERE date < :date');
        $result->bindParam(':date', $date, PDO::PARAM_INT);

        return $result->execute();
    }
}