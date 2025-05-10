<?php defined('BILLINGMASTER') or die;

class CallPassword {

    use ResultMessage;

    /**
     * ПОЛУЧИТЬ СТАТУС
     * @return mixed
     */
    public static function getStatus()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT enable FROM ".PREFICS."extensions WHERE name = 'callpassword'");
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
        $result = $db->query("SELECT params FROM ".PREFICS."extensions WHERE name = 'callpassword'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['params'] : false;
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
        $sql .= ($status !== null ? ', enable = '.intval($status) : '')." WHERE name = 'callpassword'";
        
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ВЫВОДИТЬ ИЛИ НЕТ КНОПКУ ДЛЯ ПОДТВЕРЖДЕНИЯ ТЕЛЕФОНА
     * @param $user
     * @param null $params
     * @return bool
     */
    public static function isShowButton($user, $params = null) {
        if ($user['confirm_phone'] && $user['phone'] == $user['confirm_phone']) {
            return false;
        }

        $cp_enabled = System::CheckExtensension('callpassword', 1);
        if (!$cp_enabled) {
            return false;
        }

        $have_groups = self::searchGroupsToActs($user['user_id'], $params);
        if (!$have_groups) {
            return false;
        }

        return true;
    }


    /**
     * ПОЛУЧИТЬ СТАТУС ДОСТУПА К УРОКАМ
     * @param $user_id
     * @return bool
     */
    public static function notAccessUser($user_id) {
        $settings = CallPassword::getSettings();
        $params = json_decode($settings, 1);

        if (!$params['params']['access_users_to_lessons']) {
            return false;
        }

        $user = User::getUserById($user_id);

        return self::isShowButton($user, $params);
    }


    /**
     * ПОИСК ГРУПП ДЛЯ КОТОРЫХ ДЕЙСТВУЕТ РАСШИРЕНИЕ
     * @param $user_id
     * @param $params
     * @return bool
     */
    public static function searchGroupsToActs($user_id, $params) {
        if (!$params) {
            $settings = CallPassword::getSettings();
            $params = json_decode($settings, 1);
        }

        $cp_user_groups = !empty($params['params']['cp_user_groups']) ? implode(',', $params['params']['cp_user_groups']) : null;

        if (!$cp_user_groups) {
            return false;
        }

        $db = Db::getConnection();
        $sql = 'SELECT COUNT(id) FROM '.PREFICS."user_groups_map WHERE user_id = $user_id AND group_id IN ($cp_user_groups)";

        $result = $db->query($sql);
        $count = $result->fetch();
        
        return $count[0] > 0 ? $count[0] : false;
    }
}