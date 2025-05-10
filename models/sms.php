<?php defined('BILLINGMASTER') or die;

class SMS {

    /**
     * @param $phone
     * @return bool
     */
    public static function validatePhone($phone) {
        if (preg_match('/^[0-9]{11,20}$/', $phone)) {
            return true;
        }

        return false;
    }


    /**
     * @param $phone
     * @param $name
     * @param $email
     * @param $password
     * @param null $settings
     * @return bool
     */
    public static function send2UserRegistration($phone, $name, $email, $password, $settings = null) {
        $phone = trim(str_replace('+', '', $phone));
        $settings = $settings ?: System::getSetting();

        if (!$settings['reg_sms']['enable'] || !$settings['reg_sms']['text'] || !self::validatePhone($phone)) {
            return false;
        }

        $replace = [
            '[NAME]' => $name,
            '[EMAIL]' => $email,
            '[PASS]' => $password,
            '[LINK]' => "{$settings['script_url']}/lk/",
        ];
        $text = strtr($settings['reg_sms']['text'], $replace);

        return SMSC::sendSMS($phone, $text);
    }


    /**
     * @param $name
     * @param $link
     * @param $phone
     * @param $text
     * @return bool
     */
    public static function sendNotice2ExpireSubs($name, $link, $phone, $text) {
        $phone = trim(str_replace('+', '', $phone));
        if (!self::validatePhone($phone)) {
            return false;
        }

        $replace = array(
            '[NAME]' => $name,
            '[LINK]' => $link
        );
        $text = strtr($text, $replace);

        return SMSC::sendSMS($phone, $text);
    }


    /**
     * ЗАПИСЬ ОТПРАВЛЕННЫХ SMS В ЛОГ
     * @param $phone
     * @param $message
     * @param null $time
     * @return bool
     */
    public static function WriteLog($phone, $message, $time = null) {
        $time = $time ?: time();
        $user_id = intval(User::isAuth()) ?: null;

        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'log_sms_send (phone, message, user_id, datetime) 
                VALUES (:phone, :message, :user_id, :datetime)';

        $result = $db->prepare($sql);
        $result->bindParam(':phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':message', $message, PDO::PARAM_STR);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':datetime', $time, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ СПИСОК отправленных SMS сообщений
     * @param bool $phone
     * @param bool $start
     * @param bool $finish
     * @return array|bool
     */
    public static function getLog($phone = false, $start = false, $finish = false) {
        $clauses = [];
        if ($phone) {
            $clauses[] = "phone LIKE '%$phone%'";
        }
        if ($start) {
            $clauses[] = "datetime > $start";
        }
        if ($finish) {
            $clauses[] = "datetime < $finish";
        }

        $db = Db::getConnection();
        $where = !empty($clauses) ? 'WHERE '.implode(' AND ', $clauses) : '';
        $limit = $where ? 2000 : 100;
        $sql = "SELECT id, phone, message, datetime FROM ".PREFICS."log_sms_send $where ORDER BY id DESC LIMIT $limit";

        $result = $db->query($sql);
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОДСЧЁТ КОЛ-ВА ЗАПИСЕЙ ЛОГА
     * @return mixed
     */
    public static function countLogs()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."log_sms_send");
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * УДАЛЕНИЕ СТАРЫХ ЛОГОВ
     * @param $date
     * @return bool
     */
    public static function delOldLogs($date) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'log_sms_send WHERE datetime < :datetime');
        $result->bindParam(':datetime', $date, PDO::PARAM_INT);

        return $result->execute();
    }
}