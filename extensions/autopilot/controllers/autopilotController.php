<?php defined('BILLINGMASTER') or die;


class autopilotController extends AdminBase {

    /**
     * АУТЕНТИФИКАЦИЯ С ВК
     */
    public function actionVkauth()
    {   
        $autopilot = Autopilot::getSettings();
        $setting = System::getSetting();

        if ($setting['enable_cabinet'] == 0 && $setting['enable_aff'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;   
        }
        
        $title = 'Вход на сайт';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';

        if (isset($_GET['code'])) {
            $errors = false;
            if (User::isAuth()) {
                header("Location: ".$setting['script_url']."/");
            } // script_url - site domain

            $params = array(
                'client_id'=>(int)$autopilot['vk_app']['id'],
                'client_secret'=>$autopilot['vk_app']['secret'],
                'redirect_uri'=>$setting['script_url'].$autopilot['vk_auth_params']['redirect_uri'],
                'code'=>$_GET['code']
            );

            $auth_str = Autopilot::skyCurl('https://oauth.vk.com/access_token',$params);
            $auth_data = $auth_str?json_decode($auth_str,true):array('error_description'=>$auth_str);
            // echo "<pre>"; print_r($auth_data); echo "</pre>";

            if (isset($auth_data['error_description'])) {
                exit('Ошибка авторизации: '.$auth_data['error_description']);
                //$errors[] = 'Ошибка авторизации: '.$auth_data['error_description'];
            }
            
            if (!isset($auth_data['user_id']) || !isset($auth_data['access_token'])) {
                echo 'Ошибка авторизации: ВКонтакте не вернул обязательные данные. Ответ VK:';
                echo "<pre>"; print_r($auth_data); echo "</pre>"; exit;                
            } 

            // set vars
            $vk_access_token = $auth_data['access_token'];
            $vk_user_id = (int)$auth_data['user_id']; 
            $email = isset($auth_data['email'])? $auth_data['email'] : ''; 

            if (!$vk_user_id) {
                $errors[] = 'Ошибка авторизации: VK ID не найден';
            }
            
            if (!$errors) {
                $user = Autopilot::getUserByField('%id'.$vk_user_id,'vk_url', $email, true);
                
                $user_data = array();
                if ($user == false) {
                    $vk_data_res = Autopilot::vk_request($vk_access_token, 'users.get', array('fields'=>'city,screen_name,contacts,photo_100'), $autopilot['vk_app']['v']);
                    $vk_data = (isset($vk_data_res['response']) && $vk_data_res['response'][0])?$vk_data_res['response'][0]:false;

                    if ($vk_data && isset($vk_data['first_name'])) {
                        $phone = isset($vk_data['mobile_phone']) ? $vk_data['mobile_phone'] : '';
                        if (!$phone && isset($vk_data['home_phone'])) {
                           $phone = $vk_data['home_phone'];
                        }

                        $name = isset($vk_data['first_name']) ? $vk_data['first_name']:null;
                        $city = (isset($vk_data['city']) && isset($vk_data['city']['title']) ) ? $vk_data['city']['title'] : null;
                        
                        $user_fields = [];
                        $user_fields['vk_url'] = 'https://vk.com/id'.$vk_user_id;
                        $user_fields['user_name'] = $name;
                        $user_fields['surname'] = (isset($vk_data['last_name'])) ? $vk_data['last_name']:null;
                        $user_fields['email'] = $email?$email:$vk_user_id.'@vk.com';
                        $user_fields['photo_url'] = (isset($vk_data['photo_100']) && $vk_data['photo_100']) ? $vk_data['photo_100'] : null;
                        $user_fields['phone'] = $phone ? preg_replace('/[^0-9+]/', '', $phone) : null;
                        $user_fields['city'] = $city;
                        $user_fields['enter_method'] = 'api';
                        $user_fields['status'] = 1;  

                        $user_import_result = Autopilot::saveUser('vk_url', $user_fields['vk_url'], $user_fields, array(),0,$setting['register_letter'],1);
                        if ($name !== null && $user_import_result['success']) {
                            $user = $user_import_result['user'];
                            $user['user_id'] = $user['id'];
                            $user['user_name'] = $user['name'];
                        } else{
                            $errors[] = 'Не получилось сохранить данные пользователя в системе.';
                        } 
                    } else{
                        $errors[] = 'Не получилось достать данные аккаунта из ВКонтакте.';
                    }                        
                }

                if (strpos($user['email'], '@vk.com') && $email) {
                    $user = Autopilot::updateUserFields($user, array('email'=>$email));
                }
            
                if (!$user || !isset($user['user_id'], $user['user_name'])) {
                    $errors[] = 'Авторизация или регистрация через ВКонтакте не удалась. Попробуйте войти еще раз, или авторизоваться по паролю, или обратитевь в поддержку.';
                } else {
                    User::Auth($user['user_id'], $user['user_name']);
                    $courses = System::CheckExtensension('courses', 1);
                    $trainingext = System::CheckExtensension('training', 1);
                    if ($setting['login_redirect'] == 1) {
                        header ("Location: ".$setting['script_url'].'/lk/');
                    } elseif ($setting['login_redirect'] == 2) {
                        header ("Location: ".$setting['script_url'].'/lk/orders');
                    } elseif ($setting['login_redirect'] == 3 && $courses == true) {
                        header ("Location: ".$setting['script_url'].'/lk/mycourses');
                    } elseif ($setting['login_redirect'] == 4 && $trainingext == true) {
                        header ("Location: ".$setting['script_url'].'/lk/mytrainings');
                    } elseif ($setting['login_redirect'] == 5) {
                        header ("Location: ".$setting['script_url']);
                    } else header ("Location: ".$setting['script_url']);
                }
            }

            require_once (ROOT . '/template/'.$setting['template'].'/views/users/login.php');
        } else{
            $autopilot['vk_auth_params']['redirect_uri'] = $setting['script_url'].$autopilot['vk_auth_params']['redirect_uri'];
            $autopilot['vk_auth_params']['client_id'] = $autopilot['vk_app']['id'];
            $autopilot['vk_auth_params']['v'] = $autopilot['vk_app']['v'];
            header('Location: https://oauth.vk.com/authorize?'.urldecode(http_build_query($autopilot['vk_auth_params'])));
            exit;
        }
    }


    /**
     * API
     */
    public function actionApi() {
        header('Content-type: application/json; charset=utf-8');
        $setting = System::getSetting();

        $import_result = array('success'=>0,'user'=>array(), 'error_message'=>'Unknown error');
        $data = array_replace_recursive($_GET,$_POST);

        // security check
        if (isset($data['key']) && $data['key'] && $data['key']==$setting['secret_key']) {
            // get data from request
            $vk_url = null;  

            if (isset($data['vk']) && $data['vk']) {
                $vk_url = Autopilot::prepareVkUrl($data['vk']);
            }

            $user_data['vk_url'] = $vk_url;
            $user_data['email'] = (isset($data['email']) && $data['email']) ? $data['email'] : null;
            $user_data['user_name'] = (isset($data['name']) && $data['name']) ? $data['name'] : null;

            if (isset($data['first_name']) && $data['first_name']) {
                $user_data['user_name'] = $data['first_name'];
            }

            if (isset($data['last_name']) && $data['last_name']) {
                $user_data['surname'] = $data['last_name'];
            }

            if (isset($data['phone']) && $data['phone']) {
               $user_data['phone'] = preg_replace('/[^0-9+]/', '', $data['phone']);
            }

            $fields = ['city','address','zipcode','note','is_client','status','points','surname','nick_telegram','nick_instagram','sex','level','bith_day','bith_month','bith_year','refer','photo_url','from_id'];
            
            foreach ($fields as $field) {
                if (isset($data[$field]) && $data[$field]) {
                    $user_data[$field] = $data[$field];
                }
            }

            if (!isset($user_data['refer']) || !$user_data['refer']) {
                $user_data['refer'] = 'https://autopilot.pro';
            }

            if (isset($data['pid']) && (int)$data['pid']) {
                $user_data['from_id'] = (int)$data['pid'];
            }

            $responder = (isset($data['responder']) && (int)$data['responder']) ? (int)$data['responder'] : 0;
            $groups = (isset($data['groups']) && $data['groups']) ? array_map('trim', explode(',', $data['groups'])) : array();
            $send_letter = (isset($data['send_letter']) && (int)$data['send_letter']) ? (int)$data['send_letter'] : 0;      

            $user_data['enter_time'] = 0;
            $user_data['channel_id'] = Autopilot::searchChannel($data);
            $user_data['enter_method'] = 'api';
            $user_data['status'] = 1;     

            $import_result = Autopilot::saveUser('vk_url', $vk_url, $user_data, $groups, $responder, $setting['register_letter'], $send_letter);

            // order part 
            if (isset($data['prod_id']) || isset($data['prod_alias'])) {
                $product = array();
                if (isset($data['prod_alias']) && $data['prod_alias']) {
                    $product = Product::getProductDataByAlias(trim($data['prod_alias']));                    
                } elseif (isset($data['prod_id']) && (int)$data['prod_id']) {
                    $prod_id = intval($data['prod_id']);
                    $product = Product::getProductById($prod_id);
                }

                // final check the product
                if (!$product || !isset($product['product_id']) ) {                    
                    $import_result['success'] = 0; 
                    $import_result['error_message'] .= ' Product not found'; 
                } elseif (!isset($import_result['user']['id'])) {
                    $import_result['success'] = 0; 
                    $import_result['error_message'] .= ' User not found for order'; 
                } else{
                    $partner_id = null;
                    $user = $import_result['user'];
                    $user_id = intval($user['id']);
                    $prod_id = intval($product['product_id']);

                    $price = Price::getFinalPrice($prod_id);
                    $real_price = (float)$price['real_price'];
                    $custom_price = isset($data['price']) ? (float)$data['price'] : false;

                    if ($custom_price !== false && $custom_price < $real_price ) {
                        $real_price = $custom_price;
                    }

                    $promo = isset($data['promo']) ? htmlentities(trim($data['promo'])) : null;

                    $client  = @$_SERVER['HTTP_CLIENT_IP'];
                    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
                    $remote  = @$_SERVER['REMOTE_ADDR'];
                     
                    if (filter_var($client, FILTER_VALIDATE_IP)) {
                        $ip = $client;
                    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
                        $ip = $forward;
                    } else {
                        $ip = htmlentities($remote);
                    }

                    if (isset($data['pid'])) {                
                        $partnership = System::CheckExtensension('partnership', 1);
                        if ($partnership) {                            
                            $partner_id = intval($data['pid']);
                            $verify = Aff::PartnerVerify(intval($partner_id));
                            $partner_id = $verify && $user_id && (int)$verify['user_id'] != $user_id ? $partner_id : null;;
                        }                        
                    } else {
                        // Если в запросе партнёр не указан, то проверяем закрепление партнёра за юзером через БД                            
                        $from_id = false;
                        if ($user && $user['from_id']) {                                  
                            $aff_set = unserialize(System::getExtensionSetting('partnership'));
                            $aff_life = intval($aff_set['params']['aff_life']) * 86400;
                            $period = time() - $user['reg_date'];                                
                            if ($period < $aff_life) {
                                $from_id = intval($user['from_id']);
                            }
                        }
                    }

                    // Запись заказа в БД
                    $sale_id = $price['sale_id'];
                    $base_id = 0; 
                    $var = null;
                    $status = isset($data['state'])?(int)$data['state']:0;
                    $comment = (isset($data['comment']) && $data['comment'])?$data['comment']:'Заказ создан Автопилотом';
                    $cookie = $setting['cookie']; 
                    $date = time();
                    while (Order::checkOrderDate($date)) {
                        $date = $date + 1;
                    }

                    $param = $date.';0;'.$user_data['channel_id'].';/api';
                    
                    $add_order = Order::addOrder($prod_id, $real_price, $user['name'], $user['email'], $user['phone'], $user['zipcode'], $user['city'], $user['address'], $comment, $param, $partner_id, $date, $sale_id, $status, $base_id, $var, $product['type_id'], $product['product_name'], $ip, 0);
                    if ($add_order) {
                        if ($status==1) {
                            $order = Order::getOrderData($date, 0);
                            $render = Order::renderOrder($order);
                        }

                        $import_result['order'] = array(
                            'id'=>$add_order,
                            'number'=>$date,
                            'name'=>$product['product_name'],
                            'price'=>$price['real_price'],
                            'state'=>$status,
                            'payment_link'=>$setting['script_url'].'/pay/'.$date
                        );
                    } else{
                        $import_result['success'] = 0; 
                        $import_result['error_message'] .= ' Order not created.';
                    }  
                }                 
            }
        } else{
            $import_result['error_message'] = 'Security check failed.';
        }
        
        $response = json_encode($import_result);
        exit($response);
    }
}