<?php defined('BILLINGMASTER') or die;

class orderController {
    
    
    // НАЧАЛО ОФОРМЛЕНИЯ ЗАКАЗА
    public function actionBuy($id)
    {
        $id = intval($id);
        $date = time();
        $setting = System::getSetting(); // Получаем все настройки
        if ($setting['enable_sale'] == 0) {
            exit('Продажи закрыты');
        }

        $product = Product::getProductById($id);
        if ($product) {
            if ($product['status'] == 0) {
                exit('Продажи закрыты');
            }

            if (!is_null($product['product_amt']) && $product['product_amt'] == 0) { // Доступность по количеству
                exit('Товар закончился');
            }
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }

        $user_email = isset($_POST['buy']) && !empty($_POST['email'])
            ? System::checkemaildomain(htmlentities(trim(strtolower(mb_substr($_POST['email'], 0, 50))))) : null;

        if ($product['sell_once']) { // продавать продукт один раз
            $user_id = User::isAuth();
            if ($user_id || $user_email) {
                $order = Order::getOrderByProductId2User($id, $user_id, $user_email);
                if ($order) {
                    exit('Товар можно купить только один раз');
                }
            }
        }

        $is_page = 'order';
        $title = 'Оформление заказа '.$product['product_title'];
        $meta_desc = $product['meta_desc'];
        $meta_keys = $product['meta_keys'];
        $use_css = 1;
        $cookie = $setting['cookie']; // Получаем имя для куки

        // Получить данные сопутствующих продуктов
        $related_products = Product::getRelatedProductsByID($id, 1);
		
		// Продление мембершипа
        $subs_id = 0;
		$subs_id = isset($_GET['subs_id']) ? intval($_GET['subs_id']) : null;
		
		// Нужно ли разделять фин.поток
        $org_id = Organization::getOrgByProduct($id);
        $organization = false;
        if($org_id){
            $_SESSION['org'] = Organization::getOrgData($org_id);
        }

        // Промо код
        if (isset($_POST['promo']) && !empty($_POST['promo'])) {
            $promo_code = htmlentities(trim($_POST['promo']));
            $_SESSION['promo_code'] = $promo_code;
        }

        $price = Price::getFinalPrice($id);

        // Если нажата кнопка оформить заказ
        if (isset($_POST['buy']) && !empty($_POST['email']) && isset($_POST['time']) && isset($_POST['token'])) {
            $sign = md5($id.'s+m'.$_POST['time']);
            //if($sign != $_POST['token']) exit('Error 912');
            if ($date - intval($_POST['time']) < 2) {
                exit('Error 913');
            }

            while (Order::checkOrderDate($date)) {
                $date += 1;
            }

            // Тут переопределяем поле, если есть в запросе инпут со своей ценой
            if (isset($_POST['user_price']) && !empty($_POST['user_price']) && isset($product['price_minmax'])) {
                $price_min = explode(":", $product['price_minmax'])[0];
                $price_max = explode(":", $product['price_minmax'])[1];
                if (intval($_POST['user_price']) > intval($price_max) && !empty($price_max)) {
                    $price['real_price'] = intval($price_max);
                } elseif (intval($_POST['user_price']) < intval($price_min)) {
                    $price['real_price'] = intval($price_min);
                } else {
                    $price['real_price'] = intval($_POST['user_price']);
                }
            }

            $name = htmlentities(mb_substr($_POST['name'], 0, 255));
            $name = trim($name, ",");
            if(strpos($name, 'script')) {
                exit('R Tape loading error');
            }

            $phone = isset($_POST['phone']) ? htmlentities(mb_substr($_POST['phone'],0,25)) : null;
            if ($phone) {
                $phone = preg_replace("/[^\d]/", "", $phone);
                $phone = '+'.$phone;
            }
            $surname = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : null;
            $nick_telegram = isset($_POST['nick_telegram']) ? htmlspecialchars($_POST['nick_telegram']) : null;
            $nick_instagram = isset($_POST['nick_instagram']) ? htmlspecialchars($_POST['nick_instagram']) : null;

            $index = isset($_POST['index']) ? htmlspecialchars(mb_substr($_POST['index'],0, 8)) : null;
            $city = isset($_POST['city']) ? htmlentities(mb_substr($_POST['city'],0,50)) : null;
            $address = isset($_POST['address']) ? htmlentities(mb_substr($_POST['address'],0,255)) : null;
            $comment = isset($_POST['comment']) ? htmlentities($_POST['comment']) : null;

            $param = isset($_COOKIE["$cookie"]) ? htmlentities($_COOKIE["$cookie"]) : htmlentities($_SESSION["$cookie"]);
            $ip = System::getUserIp();

            // ПАРТНЁРКА ПРИ ЗАКАЗЕ
            $partner_id = System::getPartnerId($user_email, $cookie);

            // КОРРЕКТИРОВКА СПЛИТ ТЕСТА для продуктов комплектаций
            $cookie_split = $setting['cookie'].'_split'; // Сформировали имя куки
            $var = null;
            if ($product['base_id'] != 0) { // если продукт - это комплектация основного
                $base_id = $product['base_id']; // Получить ID базового

                // Проверить куку и забрать значение
                if (isset($_COOKIE["$cookie_split"])) {
                    $cookie_arr = json_decode($_COOKIE["$cookie_split"], true);

                    if (array_key_exists($base_id, $cookie_arr)) {
                        $var = intval($cookie_arr["$base_id"]); // вариант описания

                        // Создать новую куку ID = вариант
                        $cookie_arr[$base_id] = $var;
                        setcookie("$cookie_split", json_encode($cookie_arr), time()+3600*24*30*12, '/');
                    }
                }
            } else {
                if (isset($_COOKIE["$cookie_split"])) {
                    $cookie_arr = json_decode($_COOKIE["$cookie_split"], true);
                    if (array_key_exists($id, $cookie_arr)) {
                        $var = intval($cookie_arr["$id"]); // вариант описания
                    }
                } else {
                    $var = null;
                }
            }
            
            // Запись заказа в БД
            $status = 0;
            $sale_id = $price['sale_id'];
            $base_id = 0;
            $current_utm = System::getUtm();

            $add_order_id = Order::addOrder($id, $price['real_price'], $name, $user_email, $phone, $index, $city, $address, $comment,
                $param, $partner_id, $date, $sale_id, $status, $base_id, $var, $product['type_id'], $product['product_name'],
                $ip, 0, $surname, $nick_telegram, $nick_instagram, 0, $current_utm, 0, $subs_id
            );
            

            if ($add_order_id) {
                $order = Order::getOrder($add_order_id);
                $domain = Helper::getDomain();
                $expire = $setting['order_life_time'] * 86400 + $date;
                setcookie("cl_eml", $user_email, $expire, '/', $domain);

                $client = User::getUserDataByEmail($user_email, null); // получаем данные клиента, если он есть.

                OrderTask::addTask($order['order_id'], OrderTask::STAGE_ACC_STAT); // добавление задач для крона по заказу

                // Если есть куки с именем и емейлом
                if (!isset($_COOKIE['emnam'])) {
                    $emnam = $user_email . '='.$name . '='.$phone;
                    setcookie('emnam', $emnam, time()+3600*24*30*3, '/');
                }
            
                // Если у продукта есть апселл
                if ($product['upsell_1'] != 0) {
                    if ($product['type_id'] == 2) {
                        $_SESSION["delivery_$date"] = 1; // Запуск сессии для доставки
                    }
                    
                    $_SESSION["upsell_$date"] = 1; // Запуск сессии для идентификации апселла
                    header("Location: ".$setting['script_url']."/offer/$date");
                } else {
                    if ($product['type_id'] == 2) {
                        $_SESSION["delivery_$date"] = 1;
                        header("Location: ".$setting['script_url']."/delivery/$date");
                    } else {
                        header("Location: ".$setting['script_url']."/pay/$date");
                    }
                }


                // Если есть сопутствующие товары
                if ($related_products) {
                    header("Location: ".$setting['script_url']."/related/$date");
                }
            }
        }

        require_once (ROOT . '/template/'.$setting['template'].'/views/order/buy.php');
    }
    
	
	
	// ДОСРОЧНАЯ ОПЛАТА ЗАКАЗА ПО РАССРОЧКЕ
    public function actionAheadOrder($map_id)
    {
        $map_id = intval($map_id);
        $now = time();
        $setting = System::getSetting();

        $userId = intval(User::checkLogged());

        $map_item = Order::getInstallmentMapData($map_id);

        // Создаём заказ для рассрочки
        if ($map_item['notif'] == 0) {

            // считаем сколько уже заплачено
            $pay_actions = unserialize(base64_decode($map_item['pay_actions']));
            $pay_summ = 0;
            $count = 0;
            foreach($pay_actions as $action) {
                $pay_summ = $pay_summ + $action['summ'];
                $count++;
            }
            $pay_periods = $map_item['max_periods'] - $count;

            $summ_to_pay = ($map_item['summ'] - $pay_summ)/ $pay_periods;

            $new_order = Order::createNewOrderFromInstallment($map_item['order_id'], round($summ_to_pay), $map_item['email'], $now, $map_item['id'], $map_item['installment_id']);

            if($new_order){
                $comment = "Очередной платёж по рассрочке с ID ".$map_item['id'];
                $upd = Order::updateAdminCommentByOrder($new_order, $comment, $map_item['id']);

                $order = Order::getOrderDataByID($new_order, 5);
                $upd_notif = Order::updateNotifCount($map_item['id'], 1, $order['order_date']);

                if($upd_notif) header("Location: /pay/".$order['order_date']);
            }
        }
    }



    // ДОСРОЧНОЕ ПОГАШЕНИЕ РАССРОЧКИ
    public function actionAhead($id)
    {
        $id = intval($id);
        $now = time();
        $setting = System::getSetting();

        $map_item = Order::getInstallmentMapData($id); // получить данные договора рассрочки
        $installment_data = Product::getInstallmentData($map_item['installment_id']); // получить настройки рассрочки

        // Если это первая попытка оплатить досрочно
        if ($map_item['ahead_id'] == 0) {

            $pay_actions = unserialize(base64_decode($map_item['pay_actions']));
            $pay_summ = 0;
            foreach($pay_actions as $action) {
                $pay_summ = $pay_summ + $action['summ'];
            }

            $total = $map_item['summ'] - $pay_summ;


            // Создать заказ
            $new_order = Order::createNewOrderFromInstallment($map_item['order_id'], $total, $map_item['email'], $now, $id);

            if ($new_order) {
                header("Location: /pay/".$now);
                $admin_comment = 'Досрочное погашение ID '.$id;
                $update_map = Order::updateMapFromAhead($id, $new_order, $admin_comment);
            }

        } else {

            $order_data = Order::getOrderDataByID($map_item['ahead_id'], 5);

            $order_life_time = $setting['order_life_time'] * 86400;
            if (($order_data['order_date'] + $order_life_time) > $now) header("Location: /pay/".$order_data['order_date']);
            else {

                // Удалем данные из ahead_id в карте рассрочек
                $reset = 1;
                $order_id = 0;
                $admin_comment = null;
                $update_map = Order::updateMapFromAhead($id, $order_id, $admin_comment, $reset);
            }
        }
    }



    // СОПУТСТВУЮЩИЕ ТОВАРЫ ИЛИ КОРЗИНА 2
    public function actionRelated($order_date)
    {
        $now = time();
        $setting = System::getSetting(); // Получаем все настройки
        $cookie = $setting['cookie'];
        $tax = 0;
        $added_array = array();
        $rel_array = array();

        $life_time = $setting['order_life_time'] * 86400;

        if ($order_date > ($now - $life_time)) {

            // Данные заказа по order_date
            $order_date = intval($order_date);
            $title = 'Корзина';
            $meta_desc = '';
            $meta_keys = '';
            $use_css = 1;
            $is_page = '';


            // Если продукт добавлен к заказу
            if (isset($_POST['add_offer'])) {

                $offer_id = intval($_POST['offer_id']);
                $related = Product::getRelatedItemByID($offer_id);
                if ($related) {
                    $product_data = Product::getProductById($related['product_id']);
                    $update = Order::UpdateOrderAfterUpsell($order_date, $related['product_id'], $related['price'], $product_data['type_id'], $product_data['product_name']);
                    if ($update) header("Location: /related/$order_date");
                } else exit('Error product added');


            }

            // Получить данные заказа
            $order = Order::getOrderData($order_date, 0);

            // УДАЛИТЬ ДАННЫЕ ИЗ ЗАКАЗА
            if (isset($_POST['delete_item'])) {
                $item = intval($_POST['item_id']);
                $del = Order::deleteOrderItem($order['order_id'], $item);
                if ($del) header("Location: /related/$order_date");
            }

			if ($order) {
            // Получить данные сопутствующих продуктов
            $related_products = Product::getRelatedProductsByID($order['product_id'], 1);
            if (!$related_products) header("Location: /");
            $product = Product::getProductById($order['product_id']);

            $order_items = Order::getOrderItems($order['order_id']);
			} else exit('Не удалось получить данные заказа');

            $total = 0;
            foreach($order_items as $item) {
                $total = $total + $item['price'];
            }

            require_once (ROOT . '/template/'.$setting['template']. '/views/order/related.php');




        } else exit('Время заказа истекло');
    }


    /**
     * АПСЕЛЛЫ
     * @param $id
     * @return bool
     */
    public function actionOffer($id)
    {
        if (isset($_SESSION["upsell_$id"])) { // ПРОВЕРКА СЕССИИ
            $id = intval($id);
            $setting = System::getSetting(); // Получаем все настройки
            if ($setting['enable_sale'] == 0) {
                exit('Продажи закрыты');
            }

            $title = 'Спецпредложение';
            $meta_desc = $meta_desc = $meta_keys = $is_page = '';
            $use_css = 1;

            $order = Order::getOrderData($id, 0); // Получить данные заказа
            if (!$order) {
                exit('Ошибка. Заказ не найден');
            }

            $product = Product::getProductUpsellData($order['product_id']); // Получить ID апсельных продуктов
            $step = isset($_POST['upsell']) && isset($_POST['step']) ? (int)$_POST['step'] : 1;

            if (isset($_POST['upsell'])) {
                if ($_POST['result'] == 1) {
                    $upsell = Product::getProductData($product["upsell_{$step}"]); // Получаем цену и тип продукта
                    $price = !empty($product["upsell_{$step}_price"]) ? $product["upsell_{$step}_price"] : $upsell['price'];
                    $upd = Order::UpdateOrderAfterUpsell($id, $product["upsell_$step"], $price, // Обновляем заказ в БД
                        $upsell['type_id'], $upsell['product_name']
                    );

                    if ($step > 1 && $upsell['type_id'] == 2) {
                        $_SESSION["delivery_$id"] = 1;
                    }
                }
                $step++;
            }

            if ($step <= 3 && $product["upsell_$step"] != 0) {
                $upsell = Product::getProductData($product["upsell_{$step}"]); // Получить данные продукта апселла
                if ($upsell) {
                    if ($step == 1 && $upsell['type_id'] == 2) {
                        $_SESSION["delivery_$id"] = 1;
                    }

                    $intro = $product["upsell_{$step}_desc"];
                    $text = $product["upsell_{$step}_text"];
                    $price = !empty($product["upsell_{$step}_price"]) ? $product["upsell_{$step}_price"] : $upsell['price'];
                    $old_price = !empty($product["upsell_{$step}_price"]) ? $upsell['price'] : false;

                    require_once (ROOT . '/template/'.$setting['template']. '/views/order/upsell.php');
                    return true;
                } else { // Если продукт удалён, отправляем письмо админу
                    Email::AdminNotification($setting['admin_email'], $id);
                }
            }

            OrderTask::addTask($order['order_id'], OrderTask::STAGE_UPSELL); // добавление задач для крона по заказу

            $url = isset($_SESSION["delivery_$id"]) ? "/delivery/$id" : "/pay/$id";
            System::redirectUrl($url);
        } else {
            exit('Что-то не так, ошибка сессии');
        }
    }




    // ДОСТАВКА ВЫБОР
    public function actionDelivery($order_date)
    {
        if (!isset($_SESSION["delivery_$order_date"])) exit('Ошибка сессии');
        $setting = System::getSetting();
        $js = 1;

        // Данные заказа по order_date
        $order_date = intval($order_date);
        $title = 'Выбор способа доставки';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = '';


        // Получить данные заказа
        $order = Order::getOrderData($order_date, 0);

        // Получить список продуктов заказа
        if ($order) {
            $order_items = Order::getOrderItems($order['order_id']);
        } else {
            exit('Не удалось получить данные заказа');
        }

        // Получить список способов доставки
        $delivery_methods = Order::getDeliveryMethods();

        if (isset($_POST['delivery_ok']) && isset($_POST['method']) && $_POST['pay']!= null ) {
            $method = intval($_POST['method']);
            $pay = intval($_POST['pay']);
            $total = intval($_POST['total']);


            if ($pay == 1) {
                $upd = Order::UpdateOrderDeliveryMethod($order_date, $method);
                System::redirectUrl("/pay/$order_date");
            }

            if ($pay == 0) {
                $metod_name = Order::getDeliveryMethodName($method);
                $i = 1;
                $o_item = array();
                foreach($order_items as $item) {
                    $o_item[$i] = $item['product_name'];
                    $i++;
                }

                $upd = Order::UpdateOrderDeliveryMethod($order_date, $method);
                $send = Email::SendConfirmDelivery($order_date, $order['client_name'], $order['client_email'], $o_item, $total, $metod_name);
                require_once (ROOT . '/template/'.$setting['template']. '/views/order/confirm_delivery.php');
                return true;
            }



        } elseif (isset($_POST['delivery_ok']) && !isset($_POST['method'])) {
            $message = 'Выберите способ доставки';
        } elseif (isset($_POST['delivery_ok']) && $_POST['pay'] == null) {
            $message = 'Выберите вариант оплаты';
        }

        require_once (ROOT . '/template/'.$setting['template']. '/views/order/delivery.php');
    }



    // ПОДВТЕРЖДЕНИЕ ДОСТАВКИ
    public function actionConfirmdelivery($order_date)
    {
        if (isset($_GET['key'])) {

            $key = htmlentities($_GET['key']);

            // Получить данные заказа
            $order = Order::getOrderData($order_date, 0);

            // Сверить емейл в ключе и в заказе
            if (md5($order['client_email']) == $key) {

                $setting = System::getSetting();

                // Изменить статус заказа на 7
                $upd = Order::UpdateOrderDeliveryConfirm($order_date, 7);

                // Отправить письмо админу
                Email::AdminDeliveryConfirm($order_date, $order['client_email'], $order['client_name']);

                $title = 'Выбор способа доставки';
                $meta_desc = '';
                $meta_keys = '';
                $use_css = 1;
                $is_page = '';
                require_once (ROOT . '/template/'.$setting['template']. '/views/order/delivery_confirmed.php');

            } else {
                exit('Неверный ключ');
            }
        } else {
            System::redirectUrl('/');
        }
    }



    // ОТМЕНА ЗАКАЗА
    public function actionCancelpay($order_date)
    {
        // Проверить дату заказа и текущую дату (в настройках получить сколько времени хранить заказ)
        $now = time();
        $setting = System::getSetting(); // Получаем все настройки
        $cookie = $setting['cookie'];
        $tax = 0;
        $back = $setting['script_url'].'/lk/orders';

        $life_time = $setting['order_life_time'] * 86400;

        if ($order_date > ($now - $life_time)) {

            // Данные заказа по order_date
            $order_date = intval($order_date);
            $title = 'Выбор способа оплаты';
            $meta_desc = '';
            $meta_keys = '';
            $use_css = 1;
            $is_page = 'order';

            // Получить данные заказа
            $order = Order::getOrderData($order_date, 0);

            $hash = md5($order['client_email'].':'.$order_date);

            if (isset($_GET['key']) && $_GET['key'] == $hash) {
                $cancel = Order::deleteOrder($order['order_id']);
                if ($cancel) {
                    echo '<script>alert("Заказ удалён"); document.location.href = "'.$back.'";</script>';
                }
            } else {
                header("Location: /");
                exit();
            }

        } else {

            header("Location: /");
            exit();
        }

    }


    /**
     * ОПЛАТА
     * @param $order_date
     * @return bool
     */
    public function actionPay($order_date)
    {
        $noindex = true;

        // Проверить дату заказа и текущую дату (в настройках получить сколько времени хранить заказ)
        $now = time();
        $setting = System::getSetting(); // Получаем все настройки
        $cookie = $setting['cookie'];
        $jquery_head = 1;
        $tax = 0;

        $life_time = $setting['order_life_time'] * 86400;

		$order_date = intval($order_date);
		// Получить данные заказа
        $order = Order::getOrderData($order_date, 0, 1);
        
        if(!$order) exit('Заказ не найден');

		if ($order['installment_map_id'] != 0) { // если рассрочка, то lifetime считается на 100 дней
		    $life_time = 100*86400;
        }

        if ($order_date > ($now - $life_time)) {
            // Данные заказа по order_date
            $title = 'Выбор способа оплаты';
            $meta_desc = $meta_keys = '';
            $use_css = 1;
            $is_page = 'order';

            if (!isset($_SESSION['admin_token']) && (!isset($_COOKIE['cl_eml']) || $_COOKIE['cl_eml'] !== $order['client_email'])) {
                $hide_cl_email = true;
            }

            if ($order['ship_method_id'] != null) {
                $ship_method = System::getShipMethod($order['ship_method_id']);
                if ($ship_method['tax'] != 0) {
                    $tax = $ship_method['tax'];
                }
            }

            // Получить список продуктов заказа
            if ($order) {
                $order_items = Order::getOrderItems($order['order_id']);
                if (!$order_items) {
                    exit('Items no found');
                }
            } else {
                exit('Не удалось получить данные заказа');
            }

            $membership = System::CheckExtensension('membership', 1);
            $total = 0;
            $related_products = false;
            $get_member = false;

            $prod_ids = array_column($order_items, 'product_id');
            $prod_names = array_column($order_items, 'product_name');
            $recurrent_enable = false;

            $client = User::getUserDataByEmail($order['client_email'], null);
            $installments2order = true;
            foreach($order_items as $key => $item) {
                $product_data = Product::getProductData($item['product_id']); // получаем данные продукта подписки
				if (!$product_data) {
					exit('<p style="text-align:center">К сожалению этот заказ не может быть оплачен, товар снят с продаж<br /><a href="/">Вернуться на главную</a></p>');
				}

                if ($membership && $item['type_id'] == 3 && $product_data) {
                    $get_member = 1;
					$plane = Member::getPlaneByID($product_data['subscription_id']);
	
					if ($plane['recurrent_enable'] == 1) {
					    $recurrent_enable = true;
                    }
                    
                    // Проврека активной попдиски, если разрешено продлевать только активные
                    if ($order['subs_id']!= 0 && $plane['prolong_active'] == 1) {
                        $map = Member::getUserMemberMapByID($order['subs_id']);
                        if($map['status'] == 0){
                            // завершаем оформление
                            $page['tmpl'] = 1;
                            $page['name'] = $page['custom_code'] = null;
                            if($plane['prolong_link'] != null) $act = '<a href="'.$plane['prolong_link'].'">Перейдите по этой ссылке</a>';
                            else $act = 'Свяжитесь с нами';
                            $page['content'] = '<p style="text-align:center; padding:2em 0">К сожалению этот заказ не может быть оплачен, срок продления истёк.<br />'.$act.'.</p>';
                            
                            require_once (ROOT . '/template/'.$setting['template'].'/views/static/static.php');
                            // удалить заказ
                            $del = Order::deleteOrder($order['order_id']);
                            return true;
                        }
                    }
                }
                
                if ($key == 0) {
                    $related_products = Product::getRelatedProductsByID($item['product_id'], 1);
                }
                
                $total += $item['price'];
            }


            // ЕСЛИ ЗАКАЗ 0 рублей
            if ($total == 0) {
                // рендерим заказ
                $render = Order::renderOrder($order);
                $redirect = false;
                
                foreach($order_items as $item) {
                    $product = Product::getProductData($item['product_id']);
                    if ($product['redirect_after']) {
                        $redirect = $product['redirect_after'];
                    }
                }
                
                if ($redirect) {
                    System::redirectUrl($redirect);
                }
                
                // Если в настройках указано давать скачивать сразу
                // то выдаём страницу download.php
                // Если нет, то отправляем заказ на емейл.
                if ($setting['simple_free_dwl'] == 1) {
                    require_once (ROOT . '/template/'.$setting['template'].'/views/order/free_load.php');
                    return true;
                } else {
                    require_once (ROOT . '/template/'.$setting['template'].'/views/order/thanks.php');
                    return true;
                }
            } else {
                $products_ids = array_column($order_items, 'product_id');
                $installment_list = Installment::getInstallments2Products($products_ids, 0, $total);
                $prepayment_list = Installment::getInstallments2Products($products_ids, 1, $total);
            }
            
            
            
            // РУЧНОЙ СПОСОБ
            if (isset($_POST['custom_pay']) && isset($_COOKIE["$cookie"])) {
                
                $payment_id = intval($_POST['payment']);
                $gateway = htmlentities($_POST['gateway']);
                $purse = htmlentities($_POST['card_number']);
                $summ = intval($_POST['summ']);
                if (isset($_SESSION['cart'])) unset($_SESSION['cart']);
                
                // Обновить статус заказа
                $upd = Order::UpdateOrderCustom($order_date, $payment_id);       
                
                // Отправить письмо админу
                if ($upd) {
                    $send = Email::AdminCustomOrder($order_date, $setting['secret_key'], $setting['admin_email'], $order['client_email'], $gateway, $purse, $summ, $order['client_name'], $order['client_phone'], $setting['script_url'], $order['order_id']);
                }
                
                $custom_data = Order::getDataCustomModule();
                require_once (ROOT . '/payments/custom/success.php');
                return true;
                
            }
            
            
            
            // ОПЛАТА ОТ ОРГАНИЗАЦИИ
            if (isset($_POST['company_pay']) && isset($_COOKIE["$cookie"])) {
                
                $payment_module = Order::getDataCustomModule('company'); // Данные платёжки
                $payment_params = unserialize(base64_decode($payment_module['params'])); // извлечь параметры
                
                $payment_id = intval($_POST['payment']);
                $organization = htmlentities($_POST['organization']);
                $inn = intval($_POST['inn']);
                $summ = intval($_POST['summ']);
                if (isset($_POST['bik'])) $bik = htmlentities($_POST['bik']);
                else $bik = null;
                if (isset($_POST['rs'])) $rs = htmlentities($_POST['rs']);
                else $rs = null;
                if (isset($_POST['country'])) $country = htmlentities($_POST['country']);
                else $country = null;
                if (isset($_POST['city'])) $city = htmlentities($_POST['city']);
                else $city = null;
                if (isset($_POST['address']))$address = htmlentities($_POST['address']);
                else $address = null;				 
                if (isset($_SESSION['cart'])) unset($_SESSION['cart']);
                
                $payment_data = array();
                $payment_data = unserialize(base64_decode($order['order_info']));
                $payment_data['rs'] = $rs;
                $payment_data['city'] = $city;
                $payment_data['address'] = $address;
                
                $payment_data['org'] = $organization;
                $payment_data['inn'] = $inn;
                $payment_data['bik'] = $bik;
                
                $payment_data = base64_encode(serialize($payment_data));
                
                // Обновить статус заказа на ручной 
                $upd = Order::UpdateOrderCustom($order_date, $payment_id, $payment_data);
                
                // Отправить письмо админу
                if ($upd) {
                    $send = Email::AdminCompanyOrder($order_date, $setting['secret_key'], $setting['admin_email'], $order['client_email'], $summ, 
                    $order['client_name'], $order['client_phone'], $setting['script_url'], $order['order_id'], $organization, $inn, $bik,
                    $rs, $country, $city, $address);
                }
                
                require_once (ROOT . '/payments/company/success.php');
                return true;
                
            }
            
            
            
            // Получить список платёжных модулей
            $payments = Order::getPayments();
            if (count($order_items) == 1 && $product_data['select_payments']) {
                $select_payments = unserialize($product_data['select_payments']);
                foreach($payments as $key => $value){
                    if (in_array($value['payment_id'],$select_payments)) {
                        continue;
                    } else {
                        unset($payments[$key]);
                    }
                }
            }

            $total += $tax;
            require_once (ROOT . '/template/'.$setting['template']. '/views/order/payments.php');
            
            
            
            
            
            
            if (isset($_SESSION["upsell_$order_date"])) {
                unset($_SESSION["upsell_$order_date"]);
                //session_destroy();
                
            } if (isset($_SESSION["delivery_$order_date"])) {
                unset($_SESSION["delivery_$order_date"]);
                //session_destroy();
            }
        
        } else {
            exit('Время заказа истекло');
        }
    }


    /**
     * РАССРОЧКА
     */
    public function actionInstallment()
    {
        $now = time();
        $noindex = true;
        $setting = System::getSetting(); // Получаем все настройки
        $cookie = $setting['cookie'];
        $jquery_head = 1;
        $tax = 0;
        $life_time = $setting['order_life_time'] * 86400; // Проверить дату заказа и текущую дату (в настройках получить сколько времени хранить заказ)
        
        if (isset($_POST['installment']) && isset($_POST['installment_id']) && isset($_POST['order_date'])) {
            $order_date = intval($_POST['order_date']);
            $installment_id = intval($_POST['installment_id']);
            
            if ($order_date > ($now - $life_time)) {
                $title = 'Заявка на рассрочку';
                $meta_desc = '';
                $meta_keys = '';
                $use_css = 1;
                $is_page = '';
                $related_products = false;
                $order = Order::getOrderData($order_date, 0); // Получить данные заказа

                if ($order) {
                    $order_items = Order::getOrderItems($order['order_id']); // Получить список продуктов заказа
                    if (!$order_items) {
                        exit('Items no found');
                    }
                } else {
                    exit('Не удалось получить данные заказа');
                }

                $_SESSION['installment'] = 1;
                $installment = false;
                $total = $tax;

                foreach($order_items as $item) {
                    $total += $item['price'];
                }

                $installment_data = Product::getInstallmentData($installment_id);
                if ($installment_data) {
                    OrderTask::addTask($order['order_id'], OrderTask::STAGE_INSTALLMENT, $installment_id);
                    $installment_total = $total + $installment_data['increase'];
                }

                require_once (ROOT . '/template/'.$setting['template']. '/views/order/installment.php');
                exit;
            }
        }
        
        
        // Отправка заявки на рассрочку
        if (isset($_POST['go_installment']) && isset($_SESSION['installment']) && isset($_POST['order_date'])) {
            $order_date = intval($_POST['order_date']);

            if ($order_date > ($now - $life_time)) {
                $order = Order::getOrderData($order_date, 0); // Обновить статус заказа
                if (!$order) {
                    exit('Order Error');
                }
                
                $order_items = Order::getOrderItems($order['order_id']);
                if (!$order_items) {
                    exit('Items no found');
                }

                $total = 0;
                foreach($order_items as $item) {
                    $total = $total + $item['price'];
                }
                
                $name = htmlentities(trim($_POST['name']));
                $soname = isset($_POST['soname']) ? htmlentities($_POST['soname']) : null;
                $otname = isset($_POST['otname']) ? htmlentities($_POST['otname']) : null;
                $passport = isset($_POST['passport']) ? htmlentities($_POST['passport']) : null;
                $email = $order['client_email'];
                $phone = isset($_POST['phone']) ? htmlentities($_POST['phone']) : null;
                $city = isset($_POST['city']) ? htmlentities($_POST['city']) : null;
				$address = isset($_POST['address']) ? htmlentities($_POST['address']) : null;
                $install_id = intval($_POST['install_id']);
                $install_title = htmlentities($_POST['install_title']);
                
                $installment_data = Product::getInstallmentData($install_id);
                if (!$installment_data) {
                    exit('Wrong installment data');
                }

                $letters = unserialize(base64_decode($installment_data['letters']));
                $sms = unserialize(base64_decode($installment_data['sms']));
                
                $good = $setting['script_url'].'/installment/vote?key='.md5($setting['secret_key']).'&order='.$order['order_id'].'&install_id='.$install_id.'&answer=1';
                $bad = $setting['script_url'].'/installment/vote?key='.md5($setting['secret_key']).'&order='.$order['order_id'].'&install_id='.$install_id.'&answer=0';
                
                $url = false;
                $url2 = false;
                
                if (isset($_FILES['skan']) && $_FILES["skan"]["size"] != 0) {
                    $fd = mkdir(ROOT ."/tmp/$now/", 0755);
                    $tmp_name = $_FILES["skan"]["tmp_name"]; // Временное имя картинки на сервере
                    $img = $_FILES["skan"]["name"]; // Имя картинки при загрузке 
                    
                    $folder = ROOT . "/tmp/$now/"; // папка для сохранения
                    $path = $folder . $img; // Полный путь с именем файла
                    if (is_uploaded_file($tmp_name)) {
                        move_uploaded_file($tmp_name, $path);
                    }

                    $url = $setting['script_url'].'/tmp/'.$now.'/'.$img;
                }
                
                if (isset($_FILES['skan2']) && $_FILES["skan2"]["size"] != 0) {
                    $tmp_name = $_FILES["skan2"]["tmp_name"]; // Временное имя картинки на сервере
                    $img2 = $_FILES["skan2"]["name"]; // Имя картинки при загрузке 
                    
                    $folder = ROOT . "/tmp/$now/"; // папка для сохранения
                    $path2 = $folder . $img2; // Полный путь с именем файла
                    if (is_uploaded_file($tmp_name)) {
                        move_uploaded_file($tmp_name, $path2);
                    }

                    $url2 = $setting['script_url'].'/tmp/'.$now.'/'.$img2;
                }
                
                
                // Отправить письмо админу с заявлением
                $subject = 'Заявление на рассрочку';
                $letter = "
                <p>Рассрочка $install_title</p>
                <p>ФИО: $soname $name $otname<br />
                Email: $email<br />
                ТЕЛЕФОН: $phone<br />
                ПАСПОРТ: $passport<br />
                ГОРОД: $city<br />
                АДРЕС: $address
                </p>";
                
                if ($url) $letter .= "<p>СКАН ПАСПОРТА: <br /><a href='$url'>$url</a></p>";
                if ($url2) $letter .= "<p>СКАН ПАСПОРТА 2: <br /><a href='$url2'>$url2</a></p>";
                
                if ($installment_data['approve'] != 1) {
                    $letter .= "
                    <p>-------------</p>
                    
                    <p><a href='$good'>Одобрить рассрочку</a></p>
                    <p><a href='$bad'>Отклонить заявку</a></p>
                    ";
                }
                
                $send = Email::SendMessageToBlank($setting['admin_email'], $name, $subject, $letter);
                
                $instalment_map_status = 0;
                $admin_comment = 'Рассрочка '.$install_title. ' | Первый платёж';
                
                // Доп. стоимость рассрочки
                $increase_pay = $installment_data['increase'] > 0 ? $installment_data['increase'] / $installment_data['max_periods'] : 0;
                $order_summ = false;
                $preupd = false;
                $expired = false;
                $first = true;
				
				$total = $total + $installment_data['increase'];
                    
                // Если авто одобрение
                if ($installment_data['approve'] == 1) {
                    // Обновить статус заказа
                    $change = Order::updateStatusInstallment($order_date, 5);
                    
                    // Создать запись с данными рассрочки
                    $map = Installment::writeInstalmentMap($order['order_id'], $total, $instalment_map_status, $installment_data, $email, 0, null, null, $now);
                    
                    // Обновить сумму заказа
                    if ($map) {
                        $update = Order::updateSummForInstallment($order['order_id'], $installment_data['first_pay'], $admin_comment, $map, $order_summ, $preupd, $increase_pay, $expired, $first);
                        // Отправить на оплату заказа
                        if ($update) {
                            System::redirectUrl("/pay/$order_date");
                        }
                    } else {
                        System::redirectUrl("/pay/$order_date");
                    }
                } else {
                    if ($send) {
                        $change = Order::updateStatusInstallment($order_date, 3); // на рассмотрении
                    }
                    
                    // Создать запись с данными рассрочки
                    $map = Installment::writeInstalmentMap($order['order_id'], $total, $instalment_map_status, $installment_data, $email, 0, null, null, $now);
                    
                    // Обновить сумму заказа
                    $update = Order::updateSummForInstallment($order['order_id'], $installment_data['first_pay'], $admin_comment, $map, $order_summ, $preupd, $increase_pay, $expired, $first );
                
                    // Показать страницу спасибо.
                    $title = 'Заявка на рассрочку';
                    $meta_desc = '';
                    $meta_keys = '';
                    $use_css = 1;
                    $is_page = '';
                    
                    require_once (ROOT . '/template/'.$setting['template']. '/views/order/installment_wait.php');
                    exit;
                }
            }
        }
        
        exit('error');
    }
    
    
    // ПОДТВЕРЖДЕНИЕ РАССРОЧКИ или ОТКЛОНЕНИЕ
    public function actionVoteinstallment()
    {
        if (isset($_GET['key']) && isset($_GET['order']) && isset($_GET['install_id'])) {
            
            $now = time();
            $setting = System::getSetting();
            $hash = md5($setting['secret_key']);
            if ($hash == $_GET['key']) {
                
                $order_id = intval($_GET['order']);
                $order = Order::getOrderDataByID($order_id, 3);
                
                if ($order) {
                    
                    $installment_id = intval($_GET['install_id']);
                    $installment_data = Product::getInstallmentData($installment_id);
                    $letters = unserialize(base64_decode($installment_data['letters']));
                    $sms = unserialize(base64_decode($installment_data['sms']));
                    $phone = $order['client_phone'];
                    
                    $replace = array(
                    '[NAME]' => $order['client_name'],
                    '[EMAIL]' => $order['client_email'],
                    '[ORDER]' => $order['order_date'],
                    '[LINK]' => $setting['script_url'].'/pay/'.$order['order_date'],
                    );
                    
                    if ($_GET['answer'] == 1) {
                        
                        // ПОДТВЕРЖДЕНИЕ
                        
                        $upd = Order::updateStatusInstallment($order['order_date'], 5);
                        
                        
                        // Отправляем письмо и смс клиенту
                        
                        $text = strtr($letters['letter_good'], $replace);
                        $send = Email::SendMessageToBlank($order['client_email'], $order['client_name'], $letters['subject_good'], $text);
                       
                       
                        $message = strtr($sms['text_good'], $replace); 
                        $send_sms = SMSC::sendSMS($phone, $message);
                        
                        
                        echo '<h1>Всё ок. Рассрочка одобрена</h1>';
                        exit();
                        
                        
                    } else {
                        
                        // ОТКЛОНЕНИЕ
                        
                        $upd = Order::updateStatusInstallment($order['order_date'], 4);
                        
                        // Отылаем емейл клиенту
                        $text = strtr($letters['letter_bad'], $replace);
                        $send = Email::SendMessageToBlank($order['client_email'], $order['client_name'], $letters['subject_bad'], $text);
                        
                        if ($sms['send_bad'] == 1 && $phone != null) {
                            
                            $message = strtr($sms['text_bad'], $replace);
                            $send_sms = SMSC::sendSMS($phone, $message);
                        }
                        
                        echo '<h1>Рассрочка отклонена</h1>';
                        exit();
    
                    }
                } else exit('Order is render');
                
                
            } else exit('Wrong_response');
            
        } else exit('Wrong response');
    }
    
    
    
    
    // ПОДТВЕРЖДЕНИЕ РУЧНОЙ СПОСОБ
    public function actionConfirm()
    {
        if (isset($_GET['date']) && isset($_GET['key'])) {
            
            $order_date = intval($_GET['date']);
            $md5 = $_GET['key'];
            $setting = System::getSetting(); 
            // Найти заказ в БД по order_date
            if (isset($_GET['status']) && $_GET['status'] == 33) {
                $order = Order::getOrderData($order_date, 0);
            } else $order = Order::getOrderData($order_date, 2);
            
            if ($order) {
                if (md5($order['order_id'].$setting['secret_key']) == $md5) {
                    
                    // Обновить и обработать заказ
                    $render = Order::renderOrder($order);
                    $user_id = isset($_SESSION['admin_user']) ? intval($_SESSION['admin_user']) : 0; 
                    
                    if ($render){
                        $log = ActionLog::writeLog('orders', 'confirm', 'order', $order['order_id'], time(), $user_id, json_encode($_GET));
                        echo '<h1 style="text-align:center; padding:1em 0">Всё ок. Заказ одобрен</h1>';  
                    } 
                    else echo 'Ошибка обновления и обработки заказа';
                    
                }
            } else {
                exit('Заказ не найден');
            }
        } else exit('Ошибка передачи параметров');
    }


    /**
     * ИНФОРМАЦИЯ ПО ЗАКАЗУ
     * @param $order_date
     */
    public function actionOrderInfo($order_date) {
        $setting = System::getSetting();
        $is_page = 'order-info';
        $meta_desc = $meta_keys = $title = $h2 = $h3 = $h3_class = '';
        $use_css = 1;

        $email = isset($_GET['client_email']) ? htmlentities($_GET['client_email']) : null;
        $order = Order::getOrderData($order_date);

        if ($order && $email && $email == $order['client_email']) {
            $order_items = order::getOrderItems($order['order_id']);
            $products = Product::getProducts2Order($order['order_id']);
            $total = Order::getOrderTotalSum($order['order_id']);
            $order_info = $order['order_info'] ? unserialize(base64_decode($order['order_info'])) : null;
            $surname = isset($order_info['surname']) ? $order_info['surname'] : '';
            $client_name = $order['client_name'] . ($surname ? " $surname" : '');

            // модуль оплаты post-credit
            $pos_credit_data = Order::getPaymentSetting('poscredit');
            if ($pos_credit_data['status']) {
                $posCredit = new PosCredit();
                $profile_id = isset($_GET['profile_id']) ? (int)$_GET['profile_id'] : null;
                $pc_order = $posCredit->getOrder($order['order_id'], $profile_id);

                if ($pc_order) {
                    $title = 'Информация по кредиту';
                    $h2 = 'Заявка на рассрочку';
                    $h3 = "Статус: {$posCredit->getStatusText($pc_order['status'])}";
                    $h3_class = "pos-credit-status status-{$pc_order['status']}";
                } else {
                    require_once (ROOT . "/template/{$setting['template']}/404.php");
                }
            }
        } else {
            require_once (ROOT . "/template/{$setting['template']}/404.php");
        }

        require_once (ROOT . "/template/{$setting['template']}/views/order/info.php");
    }
    
    
    // API 
    public function actionApi()
    {
        $setting = System::getSetting();
        $params = json_decode($setting['params'], true);
        $cookie = $setting['cookie'];

        if ($setting['enable_sale'] == 0) {
            exit('Продажи закрыты');
        }

        $template = $setting['template'];
        if (isset($_REQUEST['skey']) && $_REQUEST['skey'] == $setting['secret_key'] && isset($_REQUEST['email']) && isset($_REQUEST['prod_id'])) {
            // paid api
            if (isset($_REQUEST['paid'])) {
                $sign = $_REQUEST['sign'];
                $real_sign = !empty($setting['private_key']) ? md5($setting['private_key'].';'.$_REQUEST['email']) : false;
            } else {
                $sign = false;
            }

            $valid_email = explode('@', $_REQUEST['email']);
            if (isset($valid_email[1])) {
                
                $email = htmlentities(trim(strtolower($_REQUEST['email'])));
                $replace_name = '';
                if(isset($params) && !empty($params['not_exist_name'])){
                    $replace_name = $params['not_exist_name'] == '[EMAIL]' ? $email : $params['not_exist_name'];
                }
                $name = isset($_REQUEST['name']) && !empty($_REQUEST['name']) ? htmlentities(mb_substr($_REQUEST['name'], 0, 255)) : $replace_name;
                $phone = isset($_REQUEST['phone']) ? htmlentities($_REQUEST['phone']) : null;
                
                if(strpos($name, 'script') || strpos($email, '<script')) {
                    exit('R Tape loading error');
                }
                
                $prod_id = intval($_REQUEST['prod_id']);
				$request_price = isset($_REQUEST['price']) ? intval($_REQUEST['price']) : null;
                
                if (isset($_REQUEST['promo'])) {
                    $promo = htmlentities(trim($_REQUEST['promo']));
                    $_SESSION['promo_code'] = $promo;
                } else {
                    $promo = null;
                }
                
                $browser = isset($_REQUEST['browser']) ? intval($_REQUEST['browser']) : 1;
				$comment = isset($_REQUEST['comment']) ? htmlentities($_REQUEST['comment']) : null;
          
                
                // получить данные продукта
                $product = Product::getProductById($prod_id);
                $price = Price::getFinalPrice($prod_id);

                // Получить данные сопутствующих продуктов
                $related_products = Product::getRelatedProductsByID($prod_id, 1);

                // Запись заказа в БД
                $status = 0;
                $sale_id = $price['sale_id'];
                $base_id = 0; 
                $var = null;
                $index = null;
                $city = null;
                $address = null;
                $date = time();
				$param = isset($_COOKIE["$cookie"]) ? htmlentities($_COOKIE["$cookie"]) : htmlentities($_SESSION["$cookie"]);
                //$param = $date.';0;;/api';
                $ip = System::getUserIp();
                $partner_id = System::getPartnerId($email, $cookie);
                $utm = System::getUtm($_REQUEST);

                while (Order::checkOrderDate($date)) {
                    $date = $date + 1;
                }
				
				if ($request_price != null) {
				    $price['real_price'] = $request_price;
                }
                
                $add_order = Order::addOrder($prod_id, $price['real_price'], $name, $email, $phone, $index, $city,
                    $address, $comment, $param, $partner_id, $date, $sale_id, $status, $base_id, $var,
                    $product['type_id'], $product['product_name'], $ip, 0, null, null,
                    null, 0, $utm
                );
                
                if ($add_order) {
                    $order = Order::getOrder($add_order);
                    OrderTask::addTask($order['order_id'], OrderTask::STAGE_ACC_STAT); // добавление задач для крона по заказу

                    // Если у продукта есть апселл
                    if ($product['upsell_1'] != 0) {
                        if ($product['type_id'] == 2) $_SESSION["delivery_$date"] = 1; // Запуск сессии для доставки
                        $_SESSION["upsell_$date"] = 1; // Запуск сессии для идентификации апселла
                        System::redirectUrl("{$setting['script_url']}/offer/$date");
                    } else {
                        if ($product['type_id'] == 2) {
                            $_SESSION["delivery_$date"] = 1;
                            System::redirectUrl("/delivery/$date");
                        } else {
							$order_items = Order::getOrderItems($add_order);
							$total = 0;
							$i = 0;

							foreach($order_items as $item) {
								$total = $total + $item['price'];
								$i++;
                            }

                            if ($total == 0 && $browser == 0) {
                                $render = Order::renderOrder($order);
                            } elseif ($total > 0 && isset($_REQUEST['paid'])) {
                                if ($real_sign && $sign == $real_sign) {
                                    $render = Order::renderOrder($order);
                                }
                                
                                $redirect = false;
                                if (!empty($product['redirect_after'])) {
                                    $redirect = $product['redirect_after'];
                                    if ($redirect) {
                                        System::redirectUrl($redirect);
                                    }
                                }
                            } else {
                                System::redirectUrl("/pay/$date");
                            }
                        }
                    }
                    
                    // Если есть сопутствующие товары
                    if ($related_products) {
                        System::redirectUrl("/related/$date");
                    }
                }
                
                echo 'Ok';
            } else {
                require_once (ROOT . "/template/$template/404.php");
            }
        } else {
            require_once (ROOT . "/template/$template/404.php");
        }
    }
    
    
    
    public function actionRulesinstallment($id)
    {
        $setting = System::getSetting(); // Получаем все настройки
        $title = 'Условия рассрочки';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = '';
        
        $installment_data = Product::getInstallmentData($id);
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/order/installment_rule.php');
        return true;
    }
}