<?php defined('BILLINGMASTER') or die;

class GetFunnels {
    
    /**
     * ДОБАВИТЬ СДЕЛКУ
     * @param $client_email
     * @param $client_name
     * @param $client_phone
     * @param $order_id
     * @param $amount
     * @param $prod_names
     * @param $prod_srv_names
     * @param $pay_status
     * @param null $partner_id
     * @return bool|mixed
     */
    public static function addDeal($client_email, $client_name = false, $client_phone = false, $order_id, $amount, $prod_names, $prod_srv_names, $pay_status, $partner_id = null) {
        $settings = self::getSettings();
        $params = unserialize($settings);
        
        $account_name = trim($params['params']['account_name']);
        $secret_key = trim($params['params']['secret_key']);
        $partner_id_fname = isset($params['params']['partner_id_fname']) ? trim($params['params']['partner_id_fname']) : null;
        $partner_id_fname_to_user = isset($params['params']['partner_id_fname_to_user']) ? trim($params['params']['partner_id_fname_to_user']) : null;
        $prod_srv_names_fname = isset($params['params']['prod_srv_names_fname']) ? trim($params['params']['prod_srv_names_fname']) : null;
        
        if (!$account_name || !$secret_key) {
            return false;
        }
        
        require_once(__DIR__ . '/../lib/getcourse/autoload.php');
        
        $deal = new \GetCourse\Deal();
        $deal::setAccountName($account_name);
        $deal::setAccessToken($secret_key);
    
        $prod_names = trim(html_entity_decode(implode(', ', $prod_names)));
        $prod_srv_names = !empty(array_filter($prod_srv_names, 'strlen')) ? trim(html_entity_decode(implode(', ', $prod_srv_names))) : null;
        $result = false;
        
        try {
            $deal
                ->setEmail($client_email)
                ->setOverwrite()
                ->setProductTitle($prod_names)
                ->setDealNumber($order_id)
                ->setDealCost($amount)
                ->setPaymentStatus($pay_status);
				
			if ($client_name) {
				$deal->setFirstName($client_name);
			}

			if ($client_phone) {
				$deal->setPhone($client_phone);
			}

            if ($partner_id) {
                if ($partner_id_fname_to_user) {
                    $deal->setUserAddField($partner_id_fname_to_user,  $partner_id);
                }

                if ($partner_id_fname) {
                    $deal->setDealAddField($partner_id_fname,  $partner_id);
                }
            }

            if ($prod_srv_names && $prod_srv_names_fname) {
                $deal->setDealAddField($prod_srv_names_fname, $prod_srv_names);
            }
            
            $result = $deal->apiCall('add');
        } catch (Exception $e) {
            self::writeError($e->getMessage());
        }
    
        return $result;
    }


    /**
     * ИЗМЕНИТЬ СТАТУС
     * @param $client_email
     * @param $order_id
     * @param $deal_status
     * @param $pay_status
     * @return bool|mixed
     */
    public static function changePayStatus($client_email, $order_id, $deal_status, $pay_status) {
        $settings = self::getSettings();
        $params = unserialize($settings);
    
        $account_name = trim($params['params']['account_name']);
        $secret_key = trim($params['params']['secret_key']);
    
        if (!$account_name || !$secret_key) {
            return false;
        }
    
        require_once(__DIR__ . '/../lib/getcourse/autoload.php');
    
        $deal = new \GetCourse\Deal();
        $deal::setAccountName($account_name);
        $deal::setAccessToken($secret_key);
        $result = false;
        
        try {
            $result = $deal
               ->setEmail($client_email)
                ->setDealNumber($order_id)
                ->setDealStatus($deal_status)
                ->setPaymentStatus($pay_status)
                ->apiCall('add');
        
        } catch (Exception $e) {
            self::writeError($e->getMessage());
        }
    
        return $result;
    }
    
    
    /**
     * ПОЛУЧИТЬ НАСТРОЙКИ
     * @return bool
     */
    public static function getSettings()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT params FROM ".PREFICS."extensions WHERE name = 'getfunnels'");
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
        $result = $db->query(" SELECT enable FROM ".PREFICS."extensions WHERE name = 'getfunnels'");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['enable'] : false;
    }
    
    
    /**
     * СОХРАНИТЬ НАСТРОЙКИ
     * @param $params
     * @param $status
     * @return bool
     */
    public static function saveSettings($params, $status) {
        $db = Db::getConnection();
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'getfunnels'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);

        return $result->execute();
    }
    
    
    /**
     * ЗАПИСАТЬ ОШИБКУ В ЛОГ
     * @param $error_msg
     */
    public function writeError($error_msg) {
        $error = date('d.m.Y H:i:s', time()) . " Error: $error_msg";
        file_put_contents(__DIR__ . '/../log.txt', PHP_EOL . $error, FILE_APPEND);
    }
}