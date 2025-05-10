<?php defined('BILLINGMASTER') or die;

class cartController {
    
    // ДОБАВЛЕНИЕ В КОРЗИНУ
    public function actionAdd($id)
    {
        $id = intval($id);
        
        // Проверка наличия продукта по ID 
        if(Product::getMinProductById($id)){
        
            echo Cart::AddProduct($id);
            return true;
        }
    }
    
    
    
    // УДАЛЕНИЕ ИЗ КОРЗИНЫ
    public static function actionDel($id)
    {
        $id = intval($id);
        $setting = System::getSetting();
        unset($_SESSION['cart'][$id]);
        header("Location: ".$setting['script_url']."/cart");
    }
    
    
    
    
    // ПРОСМОТР КОРЗИНЫ
    public function actionIndex()
    {
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        //unset($_SESSION['cart']);
        if ($setting['use_cart'] != 1) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }
        
        // Промо код
        if (isset($_POST['promo']) && !empty($_POST['promo'])) {
            $promo_code = htmlentities(trim($_POST['promo']));
            $_SESSION['promo_code'] = $promo_code;
        }


        $title = 'Корзина';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'cart';
        
        $product_in_cart = Cart::getProducts();
        $products_ids = $product_in_cart ? array_keys($product_in_cart) : null;
        $products = $products_ids ? Product::getProductsByIds($products_ids) : null;
        
        if (isset($_POST['buy']) && $product_in_cart == true) {
            $date = time();
            $name = htmlentities($_POST['name']);
            $email = htmlentities(trim(strtolower($_POST['email'])));
            $email = System::checkemaildomain($email);
            $phone = isset($_POST['phone']) ? htmlentities($_POST['phone']) : null;
            $index = isset($_POST['index']) ? htmlspecialchars($_POST['index']) : null;
            $city = isset($_POST['city']) ? htmlentities($_POST['city']) : null;
            $address = isset($_POST['address']) ? htmlentities($_POST['address']) : null;
            $comment = htmlentities($_POST['comment']);
            $type_id = intval($_POST['type_id']);
            $param = isset($_COOKIE["$cookie"]) ? htmlentities($_COOKIE["$cookie"]) : htmlentities($_SESSION["$cookie"]);
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = @$_SERVER['REMOTE_ADDR'];
             
            if (filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } else {
                $ip = htmlentities($remote);
            }
            
			$partner_id = null;
            
            // ПАРТНЁРКА 
            $partnership = System::CheckExtensension('partnership', 1);
            if ($partnership) {
                if (isset($_SESSION["real_aff_$cookie"])) {
                    $verify = Aff::PartnerVerify(intval($_SESSION["real_aff_$cookie"])); 
                    if ($verify && $verify['email'] != $email) { // Проверка на самозаказ
                        $partner_id = intval($_SESSION["real_aff_$cookie"]);
                    }
                } else {
                    if (isset($_COOKIE["aff_$cookie"])) {
                        $verify = Aff::PartnerVerify(intval($_COOKIE["aff_$cookie"])); // Проверка партнёра на существование и самозаказ
                        if($verify && $verify['email'] != $email) {
                            $partner_id = intval($_COOKIE["aff_$cookie"]);
                        } else {
                            $partner_id = null;
                        }
                    } elseif (isset($_SESSION["aff_$cookie"])) { // Проверка партнёра на существование
                        $verify = Aff::PartnerVerify(intval($_SESSION["aff_$cookie"]));
                        if ($verify && $verify['email'] != $email) {
                            $partner_id = intval($_SESSION["aff_$cookie"]);
                        } else {
                            $partner_id = null;
                        }
                    } else {
                        $partner_id = null;
                    }
                }
            }

            $utm = System::getUtm();
            $add_order = Cart::addCartOrder($name, $email, $phone, $index, $city, $address, $comment, $param,
                $partner_id, $date, null, 0, $type_id, $ip, $products, $utm
            );
            
            if ($add_order) {
                if (!isset($_COOKIE['emnam'])) {
                    $emnam = $email . '='.$name . '='.$phone;
                    setcookie('emnam', $emnam, time()+3600*24*30*3, '/');
                }

                OrderTask::addTask($add_order['order_id'], OrderTask::STAGE_ACC_STAT); // добавление задач для крона по заказу

                System::redirectUrl("/pay/$date");
            }
        }
        
        if (isset($_POST['checkout']) && isset($_SESSION['cart'])) {
            require_once (ROOT . '/template/'.$setting['template'].'/views/cart/checkout.php');
            
            return true;
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/cart/index.php');
        
        return true;
    }
}