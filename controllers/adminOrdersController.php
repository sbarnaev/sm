<?php defined('BILLINGMASTER') or die;

class adminOrdersController extends AdminBase {
    
    // СПИСОК ЗАКАЗОВ
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_orders'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_GET['reset']) && isset($_SESSION['filter_orders'])) {
            unset($_SESSION['filter_orders']);
            header("Location: /admin/orders");
        }
    
        $email = $status = $number = $start = $finish = $paid = $product_id = $is_filter = null;
        
        if (isset($_POST['filter'])) {
            $filter['email'] = $_POST['email'] ? htmlentities(trim($_POST['email'])) : null;
            $filter['number'] = $_POST['number'] ? intval($_POST['number']) : null;
            $filter['product_id'] = $_POST['product_id'] ? intval($_POST['product_id']) : null;
            $filter['status'] = $_POST['status'] ? $_POST['status'] : null;
            $filter['start'] = $_POST['start'] ? strtotime($_POST['start']) : null;
            $filter['finish'] = $_POST['finish'] ? strtotime($_POST['finish']) : null;
            $filter['paid'] = $_POST['paid'] ? intval($_POST['paid']) : null;
            $filter['is_filter'] = array_filter($filter, 'strlen') ? true : false;
            
            if ($filter['is_filter']) {
                $_SESSION['filter_orders'] = $filter;
            }
        } else {
            $filter = isset($_SESSION['filter_orders']) ? $_SESSION['filter_orders'] : null;
        }

        if ($filter && $filter['is_filter']) {
            foreach ($filter as $key => $value) {
                $$key = $value;
            }
        }
        
        // ПАГИНАЦИЯ
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $total_order = Order::countOrders($status, $email, $number, $start, $finish, $paid, $product_id); // кол-во заказов всего.
        $is_pagination = !isset($_POST['load_csv']) ? true : false;
        $pagination = new Pagination($total_order, $page, $setting['show_items']);
        
        $order_list = Order::getOrderAdminList($page, $setting['show_items'], $is_pagination, $status,
            $email, $number, $start, $finish, $paid, $product_id
        );
        
        if (isset($_POST['load_csv'])) {
            $time = time();
            $main_fields = ['order_id', 'order_date', 'product_id', 'product_name', 'summ', 'client_name', 'client_email', 'client_phone',
                'client_city', 'client_address', 'client_index', 'client_comment', 'admin_comment', 'partner_id',
                'status', 'payment_id', 'payment_date', 'installment_map_id'];

            $add_fields = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'utm_referrer',
                'userId_YM', 'userId_GA', 'roistat_visitor'
            ];

            $fields = array_merge($main_fields, $add_fields);
            $csv = implode(';', $fields) . PHP_EOL;
            $csv = str_replace(['order_date', 'installment_map_id'], ['order_number', 'installment_ID'], $csv);
            $count_fields = count($fields);

            $fp = fopen(ROOT.'/tmp/orders_'.$time.'.csv','w');
            fwrite($fp, $csv); // Добавляем заголовок

            foreach ($order_list as $order) {
				$summ = Order::getOrderTotalSum($order['order_id']);
                $items = Order::getOrderItems($order['order_id']);
                $order_info = $order['order_info'] ? unserialize(base64_decode($order['order_info'])) : null;
                if ($order['utm']) {
                    $utm = System::getUtmData($order['utm']);
                    $order_info = $order_info ? array_merge($order_info, $utm) : $utm;
                }

                $csv = array();
                foreach ($fields as $key => $field) {
                    if (in_array($field, $add_fields)) {
                        $value = $order_info && isset($order_info[$field]) ? $order_info[$field] : '';
                    } else {
                        if ($field == 'summ') {
                            $value = $summ; 
                        } elseif ($field == 'product_id') {
                            $value = implode('|',array_column($items,'product_id')); 
                        } elseif ($field == 'product_name') {
                            $value = implode('|',array_column($items,'product_name')); 
                        } else {
                            $value = $field == 'payment_date' && $order['payment_date'] ? date("d.m.Y H:i:s", $order['payment_date']) : $order[$field];
                        }
                    }
                    array_push($csv, $value);                   
                }
                fputcsv($fp, $csv,';');         
            }
            
            $write = fclose($fp);
            if ($write){
                System::redirectUrl("/tmp/orders_{$time}.csv");
            }
        }
            
        require_once (ROOT . '/template/admin/views/orders/index.php');
    }
    
    
    // ДОБАВИТЬ ЗАКАЗ ВРУЧНУЮ
    public static function actionAdd()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_orders'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getMainSetting();
        
        if(isset($_POST['add']) && isset($_POST['order_items']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_orders'])){
                header("Location: /admin/orders?fail");
                exit();
            }
            
            $status = 0;
            $name = htmlentities(mb_substr($_POST['name'], 0, 255));
            $email = htmlentities(trim(strtolower(mb_substr($_POST['email'], 0, 50))));
            if(isset($_POST['phone'])) $phone = htmlentities(mb_substr($_POST['phone'],0,50));
            else $phone = null;
            
            if(isset($_POST['index'])) $index = htmlspecialchars(mb_substr($_POST['index'],0, 8));
            else $index = null;
            
            if(isset($_POST['city'])) $city = htmlentities(mb_substr($_POST['city'],0,255));
            else $city = null;
            
            if(isset($_POST['address'])) $address = htmlentities(mb_substr($_POST['address'],0,255));
            else $address = null;
            
            $status = $_POST['status'];
            $comment = $_POST['admin_comment'];
            $price = intval($_POST['price']);
            
            $summ = intval($_POST['summ']);
            $date = time();
            $sale_id = null;
            $order_items = $_POST['order_items'];
            
            $add = Order::addCustomOrder($order_items[0], $date, $summ, $name, $email, $phone, $city, $address, $index, $comment, $sale_id, 
            $partner_id = null, $status, $order_items, $price);
            
            if($add){
                
                $log = ActionLog::writeLog('orders', 'add', '', 0, time(), $_SESSION['admin_user'], json_encode($_POST));
                if($status == 1){
                    // Получить данные заказа
                    $order = Order::getOrderToAdmin($add);
                    
                    // Обработка заказа
                    $render = Order::renderOrder($order);
                }
                
                if($status == 0){
                    // Отправить ссылку на оплату заказа
                }
                
                header("Location: ".$setting['script_url']."/admin/orders?success");
                
            }
            
        }
        
        require_once (ROOT . '/template/admin/views/orders/add.php');
    }
    
    
    // РЕДАКТИРОВАТЬ ЗАКАЗ
    public function actionEdit($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_orders'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        $order = Order::getOrderToAdmin($id);
        
        // Если обновление продукта заказа
        if (isset($_POST['reload_order_item'])) {
            $update_product = false;
            $order_item_id = intval($_POST['reload_order_item']);
            
            $price = isset($_POST['price']) ? intval($_POST['price']) : null;
            if ($price !== null) {
                $update_product = Order::updatePrice($order_item_id, $id, $price);
            }
            
            $prod_id = isset($_POST['prod_id']) ? intval($_POST['prod_id']) : null;
            if ($prod_id) {
                $update_product = Order::updateProductId($order_item_id, $id, $prod_id, $order['product_id']);
            }
            
            if ($update_product) {
                header("Location: /admin/orders/edit/$id?success");
                exit;
            }
        }
        
        // Если удаление продукта из заказа
        if (isset($_POST['order_item_delete']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $order_item = intval($_POST['order_item_delete']);
            $del = Order::deleteOrderItem($id, $order_item);
            if ($del) {
                header("Location: /admin/orders/edit/$id");
            }
        }
        
        // Если возврат товара из заказа
        if (isset($_POST['refund']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $product_id = intval($_POST['id']);
            $pincode = htmlentities($_POST['pin']);
            $order_item = intval($_POST['order_item']);
            $email = htmlentities($_POST['email']);
            
            $change = Order::ChangeStatus($id, $order_item); // меняем статус в таблице _order_items
            
            if ($change) {
                // получаем группы продукта и удаляем их из user_group_map
                $user = User::getUserDataByEmail($email); // данные юзера
                $product = Product::getProductById($product_id); // данные продукта
                if ($product && $product['group_id']) {
                    $delgroups = User::deleteUserGroupsFromList($user['user_id'], $product['group_id']);
                }
    
                // если есть подписка на рассылку - отписываем
                $responder = System::CheckExtensension('responder', 1);
                if ($responder && $product['delivery_id'] != 0) {
                    $delsubs = Responder::DeleteSubsRow($email, $product['delivery_id']);
                }
    
                // если есть подписка на доступ - останавливаем
                $member = System::CheckExtensension('membership', 1);
                if ($member && $product['subscription_id'] != null) {
                    // удалить планы подписок и всё что с ними связано
                    $delsub = Member::delMemberByEmail($user['user_id'], $product['subscription_id']);
                }
    
                // если начислены авторские - удаляем
                // если в настройках удалять, то если начислены комиссионные партнёрам - удаляем
                $partnership = System::CheckExtensension('partnership', 1);
                if ($partnership) {
                    $del_author_transaction = Aff::deleteAuthorTransaction($id, $product_id);
        
                    $params = unserialize(System::getExtensionSetting('partnership')); // настройки партнёрки
                    if ($params['params']['delpartnercomiss'] == 1) {
                        $del_partner_transaction = Aff::deletePartnerTransaction($id, $product_id);
                    }
                }
    
                // РАСШИРЕНИЕ GetFunnels
                if (System::CheckExtensension('getfunnels', 1)) {
                    GetFunnels::changePayStatus($order['client_email'], $id, 'waiting_for_return', 'returned');
                }
                
                header("Location: ".$setting['script_url']."/admin/orders/edit/$id?success");
            }
        }

        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_orders'])){
                header("Location: /admin");
                exit();
            }
            
            $order_date = !empty($_POST['order_date']) ? strtotime($_POST['order_date']) : 0;
            $payment_date = isset($_POST['payment_date']) ? strtotime($_POST['payment_date']) : null;
            $name = $_POST['name'];
            $email = $_POST['client_email'];
            $phone = $_POST['phone'];
            $city = $_POST['city'];
            $index = $_POST['index'];
            $address = $_POST['address'];
            $status = $_POST['status'];
            $comment = $_POST['client_comment'];
            $admin_comment = $_POST['admin_comment'];
            
            $upd = Order::updateOrderToAdmin($id, $name, $email, $phone, $city, $index, $address, $status, $comment, $admin_comment, $order_date, $payment_date);
            if ($upd) {
                $log = ActionLog::writeLog('orders', 'edit', 'order', $id, time(), $_SESSION['admin_user'], json_encode($_POST));
                header("Location: ".$setting['script_url']."/admin/orders/edit/$id?success");
            }
        }
    
        require_once (ROOT . '/template/admin/views/orders/edit.php');
    }
    
    
    // УДАЛИТЬ ЗАКАЗ
    public function actionDel($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_orders'])) header("Location: /admin");
        if(!isset($acl['del_orders'])) {
            header("Location: /admin/orders?fail");
            exit();   
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
		$setting = System::getSetting();
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Order::deleteOrder($id);
            
            if($del) {
                $log = ActionLog::writeLog('orders', 'delete', 'order', $id, time(), $_SESSION['admin_user'], 0);
                header("Location: ".$setting['script_url']."/admin/orders");   
            } else {
                header("Location: /admin/orders/edit/$id?fail");
                exit();   
            }
        }
    }


    public static function actionAddProduct($order_id) {
        if (isset($_POST['add_product']) && isset($_POST['product_id']) && isset($_POST['product_price'])) {
            $acl = self::checkAdmin();
            if (!isset($acl['change_orders'])) {
                System::redirectUrl('/admin/orders');
                exit();
            }

            $setting = System::getSetting();
            $product_price = (int)$_POST['product_price'];
            $product_id = (int)$_POST['product_id'];
            $product = Product::getProductData($product_id);
            $order = Order::getOrderToAdmin($order_id);

            if (!$order || !$product) {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            }

            $order_items = Order::getOrderItems($order_id);
            $number = count($order_items) + 1;
            $cast = $order_items[0]['cast'];

            $res = Order::addOrderItem($order_id, $product_id, $product['type_id'], $number, $product_price, $cast, $product['product_name'], $order['status']);
            if ($res) {
                $amount = Order::getOrderTotalSum($order_id);
                $upd = Order::updateOrderSum($order_id, $amount);

                if ($upd) {
                    System::redirectUrl("/admin/orders/edit/$order_id", $upd);
                }
            }
        }
    }


    public function actionDelPartner()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_orders'])) {
            header("Location: /admin");
        }

        if ($_POST['delpartner'] == true) {
            $del_partner_transaction = Aff::deletePartnerTransaction($_POST['order_id']);
            $del_partner_from_order = Aff::deletePartnerFromOrder($_POST['order_id']);
            $data = ['success' => true];
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }
}