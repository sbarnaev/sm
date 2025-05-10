<?php defined('BILLINGMASTER') or die;


class ProductHttpNotice extends Product
{
    const EVENT_TYPE_ACC_STAT = 1; // ВЫПИСКА СЧЕТА
    const EVENT_TYPE_ACC_PAY = 2; // ОПЛАТА СЧЕТА


    /**
     * ДОБАВИТЬ УВЕДОМЛЕНИЕ ДЛЯ ПРОДУКТА
     * @param $product_id
     * @param $name
     * @param $url
     * @param $send_type
     * @param $send_time_type
     * @param $vars
     * @param $is_send_utm
     * @return bool
     */
    public static function addNotice($product_id, $name, $url, $send_type, $send_time_type, $vars, $is_send_utm) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'products_http_notices (product_id, notice_name, notice_url, send_type, vars,
                    send_time_type, is_send_utm)
                VALUES (:product_id, :notice_name, :notice_url, :send_type, :vars, :send_time_type, :is_send_utm)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':notice_name', $name, PDO::PARAM_STR);
        $result->bindParam(':notice_url', $url, PDO::PARAM_STR);
        $result->bindParam(':send_type', $send_type, PDO::PARAM_INT);
        $result->bindParam(':send_time_type', $send_time_type, PDO::PARAM_INT);
        $result->bindParam(':vars', $vars, PDO::PARAM_STR);
        $result->bindParam(':is_send_utm', $is_send_utm, PDO::PARAM_INT);
        
        return $result->execute();
    }


    /**
     * РЕДАКТИРОВАТЬ УВЕДОМЛЕНИЕ ПРОДУКТА
     * @param $id
     * @param $name
     * @param $url
     * @param $send_type
     * @param $send_time_type
     * @param $vars
     * @param $is_send_utm
     * @return bool
     */
    public static function editNotice($id, $name, $url, $send_type, $send_time_type, $vars, $is_send_utm) {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."products_http_notices SET notice_name = :notice_name, notice_url = :notice_url,
                send_type = :send_type, send_time_type = :send_time_type, vars = :vars, is_send_utm = :is_send_utm 
                WHERE notice_id = $id";
        
        $result = $db->prepare($sql);
        $result->bindParam(':notice_name', $name, PDO::PARAM_STR);
        $result->bindParam(':notice_url', $url, PDO::PARAM_STR);
        $result->bindParam(':send_type', $send_type, PDO::PARAM_INT);
        $result->bindParam(':send_time_type', $send_time_type, PDO::PARAM_INT);
        $result->bindParam(':vars', $vars, PDO::PARAM_STR);
        $result->bindParam(':is_send_utm', $is_send_utm, PDO::PARAM_INT);
        
        return $result->execute();
    }
    
    // ПОЛУЧИТЬ УВЕДОМЛЕНИЯ ДЛЯ ПРОДУКТА
    public static function getNoticesToProduct($product_ids) {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."products_http_notices WHERE product_id in($product_ids) ORDER BY notice_id DESC";
        
        $result = $db->query($sql);
        
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ УВЕДОМЛЕНИЕ ПРОДУКТА
    public static function getNotice($notice_id) {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."products_http_notices WHERE notice_id = $notice_id";
        
        $result = $db->query($sql);
        
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }
    
    
    // УДАЛИТЬ УВЕДОМЛЕНИЕ ПРОДУКТА
    public static function delNotice($notice_id) {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS."products_http_notices WHERE notice_id = $notice_id";
        
        return $result = $db->query($sql);
    }
    
    
    /**
     * ОТПРАВКА УВЕДОМЛЕНИЯ
     * @param $order
     * @param $product
     * @param $prod_names
     * @param $total_summ
     * @param $setting
     * @param $event_type
     */
    public static function sendNotice($order, $product, $prod_names, $total_summ, $setting, $event_type) {
        $notices = self::getNoticesToProduct($product['product_id']);

        if ($notices) {
            $order_info = $order['order_info'] ? unserialize(base64_decode($order['order_info'])) : null;
            $user = User::getUserDataByEmail($order['client_email']);
            $notice_data_default = [
                'name' => $order['client_name'],
                'surname' => isset($order_info['surname']) ? $order_info['surname'] : '',
                'email' => $order['client_email'],
                'phone' => $order['client_phone'],
                'city' => $order['client_city'],
                'addres' => $order['client_address'],
                'index' => $order['client_index'],
                'comment' => $order['client_comment'],
                'user_id' => $user ? $user['user_id'] : '',
                'order_id' => $order['order_id'],
                'order_products' => implode(', ', $prod_names),
                'order_date' => $order['order_date'],
                'order_status' => $order['status'],
                'summ' => $total_summ,
                'product_id' => $product['product_id'],
                'product_category' => $product['cat_id'],
                'product_name' => $product['product_name'],
                'product_price' =>  $product['price'],
                'product_link' => $product['link'],
                'product_cover' => $product['product_cover'] ? "{$setting['script_url']}/images/product/{$product['product_cover']}" : '',
                'secret' => md5($setting['secret_key']),
                'userId_YM' => $order_info && isset($order_info['userId_YM']) ? $order_info['userId_YM'] : '',
                'userId_GA' => $order_info && isset($order_info['userId_GA']) ? $order_info['userId_GA'] : '',
                'roistat_visitor' => $order_info && isset($order_info['roistat_visitor']) ? $order_info['roistat_visitor'] : '',
            ];

            foreach ($notices as $notice) {
                $notice_data = [];

                if ($notice['is_send_utm']) {
                    $utm = $order['utm'] ? System::getUtmData($order['utm']) : null;
                    if ($utm) {
                        foreach ($utm as $key => $val) {
                            $notice_data[$key] = $val;
                        }
                    }
                }

                $vars = json_decode($notice['vars']);
                if (empty($vars) || !$notice['notice_url'] || $notice['send_time_type'] != $event_type) {
                    continue;
                }
				
				if (isset($vars->vk_url) && $user) {
                    $notice_data_default['vk_url'] = $user['vk_url'];
                }
    
                foreach ($vars as $name => $new_name) {
                    if ($new_name && isset($notice_data_default[$name])) {
                        $notice_data[$new_name] = $notice_data_default[$name];
                    }
                }

                if (!empty($notice_data)) {
                    $notice_url = $notice['notice_url'];

                    if ($notice['send_type'] == 2) {
                        $query_str = '';
                        foreach ($notice_data as $key => $val) {
                            $query_str .= ($query_str ? '&' : '') . "$key=".urlencode($val);
                        }

                        $notice_url .= (strpos($notice_url, '?') ? '&' : '?').$query_str;
                        System::curlAsync($notice_url);
                    } else {
                        System::curlAsync($notice_url, $notice_data);
                    }
                }
            }
        }
    }
}