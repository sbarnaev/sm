<?php

class ExpertSender {
    
    const EVENT_TYPE_ACC_STAT = 1; // ВЫПИСКА СЧЕТА
    const EVENT_TYPE_ACC_PAY = 2; // ОПЛАТА СЧЕТА
    
    
    /**
     * СОХРАНИТЬ ДАННЫЕ ДЛЯ ПРОДУКТА
     * @param $prod_id
     * @param $post
     */
    public static function saveDataToProduct($prod_id, $post) {
        if (!empty($post['rspndr_order']) || !empty($post['rspndr_pay'])) {
            $rspndr_order = intval($_POST['rspndr_order']);
            $rspndr_pay = intval($_POST['rspndr_pay']);
        
            ExpertSender::addResponder($prod_id, $rspndr_order, $rspndr_pay);
        }
    }
    
    
    /**
     * ПОЛУЧИТЬ ДАННЫЕ ДЛЯ ПРОДУКТА
     * @param $id
     * @return array|bool
     */
    public static function getDataToProduct($prod_id) {
        try {
            require_once(__DIR__ . '/expertsenderapi.php');
        
            $settings = ExpertSender::getSettings();
            $params = unserialize($settings);
            $api_url = isset($params['params']['api_url']) ? $params['params']['api_url'] : null;
            $secret_key = trim($params['params']['secret_key']);

            if (!$api_url || !$secret_key) {
                return false;
            }

            $api = new ExpertSenderApi($api_url, $secret_key);

            $list = $api->getLists();
            $rspndr = ExpertSender::getResponder($prod_id);
            
            return [
                'list' => $list,
                'rspndr' => $rspndr,
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ В ПОДПИСЧИКИ
     * @param $event_type
     * @param $product_ids
     * @param $cl_email
     * @param $cl_name
     * @return bool
     */
    public static function addSubscriber($event_type, $product_ids, $cl_email, $cl_name) {
        switch ($event_type) {
            case self::EVENT_TYPE_ACC_STAT: // ВЫПИСКА СЧЕТА
                $rspndr_key = 'rspndr_order';
                break;
            case self::EVENT_TYPE_ACC_PAY: // ОПЛАТА СЧЕТА
                $rspndr_key = 'rspndr_pay';
                break;
            default:
                return false;
        }

        try {
            require_once(__DIR__ . '/expertsenderapi.php');
    
            $settings = ExpertSender::getSettings();
            $params = unserialize($settings);
            $api_url = isset($params['params']['api_url']) ? $params['params']['api_url'] : null;
            $secret_key = trim($params['params']['secret_key']);
           
            if (!$api_url || !$secret_key) {
                return false;
            }

            $api = new ExpertSenderApi($api_url, $secret_key);

            if (!empty($product_ids)) {
                foreach ($product_ids as $prod_id) {
                    $rspndr = ExpertSender::getResponder($prod_id);

                    if ($rspndr[$rspndr_key]) {
                        $api->addSubscriber2list($rspndr[$rspndr_key], $cl_email, $cl_name);
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
    
    
    /**
     * СОХРАНИТЬ ДАННЫЕ ДЛЯ ПРОДУКТА
     * @param $product_id
     * @param null $rspndr_order
     * @param null $rspndr_pay
     * @return bool
     */
    public static function addResponder($product_id, $rspndr_order = null, $rspndr_pay = null) {
        $db = Db::getConnection();
        $sql = 'REPLACE INTO '.PREFICS.'expertsender (product_id, rspndr_order, rspndr_pay)
                VALUES (:product_id, :rspndr_order, :rspndr_pay)';
    
        $result = $db->prepare($sql);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':rspndr_order', $rspndr_order, PDO::PARAM_INT);
        $result->bindParam(':rspndr_pay', $rspndr_pay, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    
    /**
     * ПОЛУЧИТЬ СОХРАНЕННЫЕ ДАННЫЕ ДЛЯ ПРОДУКТА
     * @param $product_id
     * @return bool|mixed
     */
    public static function getResponder($product_id) {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."expertsender WHERE product_id = $product_id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }
    
    
    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ
     * @return bool
     */
    public static function getSettings()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT params FROM ".PREFICS."extensions WHERE name = 'expertsender' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
    
        return !empty($data) ? $data['params'] : false;
    }
    
    
    /**
     * ПОЛУЧИТЬ СТАТУС
     * @return bool
     */
    public static function getStatus()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT enable FROM ".PREFICS."extensions WHERE name = 'expertsender' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
    
        return !empty($data) ? $data['enable'] : false;
    }
    
    public static function saveSettings($params, $status) {
        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'expertsender'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        
        return $result->execute();
    }
}