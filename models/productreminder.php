<?php defined('BILLINGMASTER') or die;

class ProductReminder {
    
    /**
     * ДОБАВИТЬ НАПОМИНАНИЕ
     * @param $product_id
     * @param $status
     * @param $remind_letter1
     * @param $remind_letter2
     * @param $remind_letter3
     * @param $remind_sms1
     * @param $remind_sms2
     * @return bool
     */
    public static function addReminder($product_id, $status, $remind_letter1, $remind_letter2, $remind_letter3, $remind_sms1, $remind_sms2) {
        $db = Db::getConnection();

        $sql = 'INSERT INTO '.PREFICS.'products_reminders (product_id, status, remind_letter1, remind_letter2, remind_letter3,
                    remind_sms1, remind_sms2)
                VALUES (:product_id, :status, :remind_letter1, :remind_letter2, :remind_letter3,
                    :remind_sms1, :remind_sms2)';

        $result = $db->prepare($sql);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':remind_letter1', $remind_letter1, PDO::PARAM_STR);
        $result->bindParam(':remind_letter2', $remind_letter2, PDO::PARAM_STR);
        $result->bindParam(':remind_letter3', $remind_letter3, PDO::PARAM_STR);
        $result->bindParam(':remind_sms1', $remind_sms1, PDO::PARAM_STR);
        $result->bindParam(':remind_sms2', $remind_sms2, PDO::PARAM_STR);
        
        return $result->execute();
    }
    
    
    /**
     * РЕДАКТИРОВАТЬ НАПОМИНАНИЕ
     * @param $id
     * @param $status
     * @param $remind_letter1
     * @param $remind_letter2
     * @param $remind_letter3
     * @param $remind_sms1
     * @param $remind_sms2
     * @return bool
     */
    public static function editReminder($id, $status, $remind_letter1, $remind_letter2, $remind_letter3, $remind_sms1, $remind_sms2) {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."products_reminders SET status = :status, remind_letter1 = :remind_letter1,
                remind_letter2 = :remind_letter2, remind_letter3 = :remind_letter3, remind_sms1 = :remind_sms1,
                remind_sms2 = :remind_sms2  WHERE id = $id";

        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':remind_letter1', $remind_letter1, PDO::PARAM_STR);
        $result->bindParam(':remind_letter2', $remind_letter2, PDO::PARAM_STR);
        $result->bindParam(':remind_letter3', $remind_letter3, PDO::PARAM_STR);
        $result->bindParam(':remind_sms1', $remind_sms1, PDO::PARAM_STR);
        $result->bindParam(':remind_sms2', $remind_sms2, PDO::PARAM_STR);
        
        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ НАПОМИНАНИЯ ДЛЯ ПРОДУКТА
     * @param $product_id
     * @return array|bool
     */
    public static function getReminderToProduct($product_id) {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."products_reminders WHERE product_id = $product_id";

        $result = $db->query($sql);

        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ВСЕ НАПОМИНАНИЯ
     * @param null $status
     * @return bool|mixed
     */
    public static function getReminders($status = null) {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."products_reminders";
        $sql .= $status !== null ? " WHERE status = $status" : '';

        $result = $db->query($sql);

        $data = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
}