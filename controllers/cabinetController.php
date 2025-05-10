<?php defined('BILLINGMASTER') or die;

class cabinetController {
    
    // ГЛАВНАЯ СТРАНИЦА ЛИЧНОГО КАБИНЕТА
    public function actionIndex()
    {
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }

        $title = 'Личный кабинет';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        
        // Проверка авторизации
        $userId = intval(User::checkLogged());
        
        if (isset($_POST['update'])) {
            $name = htmlentities($_POST['name']);
            $phone = htmlentities($_POST['phone']);
            $zipcode = isset($_POST['zipcode']) ? htmlentities($_POST['zipcode']) : null;
            $city = isset($_POST['city']) ? htmlentities($_POST['city']) : null;
            $address = isset($_POST['address']) ? htmlentities($_POST['address']) : null;

            $surname = isset($_POST['surname']) ? htmlentities($_POST['surname']) : null;
            $nick_telegram = isset($_POST['nick_telegram']) ? htmlentities($_POST['nick_telegram']) : null;
            $nick_instagram = isset($_POST['nick_instagram']) ? htmlentities($_POST['nick_instagram']) : null;

            if (isset($_POST['vk_url']) && !empty($_POST['vk_url'])) { // расширение autopilot
                if (System::CheckExtensension('autopilot', 1)) {
                    $vk_url = Autopilot::prepareVkUrl($_POST['vk_url']);
                    $vk_url = htmlentities($vk_url);
                } else {
                    $vk_url = htmlentities($_POST['vk_url']);
                }
            } else {
                $vk_url = null;
            }

            $sex = !empty($_POST['sex']) ? htmlentities($_POST['sex']) : null;
            $day = intval($_POST['bith_day']);
            $month = intval($_POST['bith_month']);
            $year = intval($_POST['bith_year']);

            $upd = User::UpdateUserSelf($userId, $name, $phone, $zipcode, $city, $address, $surname, $nick_telegram,
                $nick_instagram, $sex, $day, $month, $year, $vk_url
            );
            if ($upd) {
                header("Location: ".$setting['script_url']."/lk?success");
                exit;
            }
        }
        
        // Данные юзера
        $user = User::getUserById($userId);
        require_once (ROOT . '/template/'.$setting['template'].'/views/users/lk.php');
    }
    
    
    // СТРАНИЦА ЗАКАЗОВ В ЛК
    public function actionOrders()
    {
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        $params = json_decode($setting['params'], true);
        $title = 'Мои заказы';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        
        // Проверка авторизации
        $userId = intval(User::checkLogged());
        
        // Данные юзера
        $user = User::getUserById($userId);
        $user_groups = User::getGroupByUser($userId);
        //print_r($user_groups);
        $orders = Order::getUserOrders($user['email'], 1);
        
        $life_time = $setting['order_life_time'] * 86400;
        $now = time();
        $time = $now - $life_time;
        $orders_nopay = Order::getUserNopayOrders($user['email'], $time, $now);
        
        if (isset($_POST['getlink'])) {
            $order_date = intval($_POST['order']);
            
            // Получить массив данных заказа по order_date со статусом 1
            $order = Order::getOrderData($order_date, 1);
            if ($order) $upd = Order::UpdateOrderDwl($order_date, time());
            
            // Вызов метода для отсылки писем клиенту
            $send = Order::getDwlOrder($order, $user_groups);
            if ($send) header("Location: ".$setting['script_url']."/lk/orders?success");
            else header("Location: ".$setting['script_url']."/lk/orders?fail");
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/users/orders.php');
        return true;
    }
    
    
    
    
    // СМЕНИТЬ ПАРОЛЬ ИЗ ЛИЧНОГО КАБИНЕТА
    public function actionChangepass()
    {
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        $title = 'Сменить пароль';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        
       // Проверка авторизации
        $userId = intval(User::checkLogged());
        
        // Данные юзера
        $user = User::getUserById($userId);
        
        if (isset($_POST['changepass'])) {
            $pass = $_POST['pass'];
            
            $change = User::ChangePass($userId, $pass);
            if ($change) header("Location: ".$setting['script_url']."/lk?success");
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/users/changepass.php');
        return true;
    }


    /**
     * СТРАНИЦА ПОДПИСОК МЕМБЕРШИПА ЮЗЕРА
     */
    public function actionMembership()
    {
        $setting = System::getSetting();
        if ($setting['enable_cabinet'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        $membership = System::CheckExtensension('membership', 1);
        if ($membership) {
            $title = 'Мои подписки';
            $meta_desc = '';
            $meta_keys = '';
            $use_css = 1;
            $is_page = 'lk';
            
            $now = time();
            
            // Проверка авторизации
            $userId = intval(User::checkLogged());
            
            // Данные юзера
            $user = User::getUserById($userId);
            
            $recurrent = 1; // получить только рекурренты
            $myplanes = Member::getRecurrentPlanesByUser($userId);
            
            if (isset($_GET['action']) && $_GET['action'] == 'pause') {
                $id = intval($_GET['id']);
                $act = Member::pauseMember($id, 0);
                if ($act) {
                    System::redirectUrl('/lk/membership', true);
                }
            }
            
            require_once (ROOT . '/template/'.$setting['template'].'/views/users/membership.php');
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }
    }
}