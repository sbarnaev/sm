<?php defined('BILLINGMASTER') or die; 


class affController {
    
    
    // РЕДИРЕКТ НА ВНЕШНИЙ ЛЕНДИНГ 
    public function actionExtland($product_id, $partner_id)
    {
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        $aff_set = unserialize(System::getExtensionSetting('partnership'));
        $aff_life = intval($aff_set['params']['aff_life']);
        
        $product = Product::getProductData($product_id);
        if($product){
            
            $product_name = $product['product_name'];
            
            $verify = Aff::AffHits($partner_id);
            if($verify){
            
                $url = $product['external_url'];
    
                if(!empty($url)){
                    setcookie("aff_$cookie", $partner_id, time()+3600*24 * $aff_life, '/');
                    header("Location: ".$url);
                } else {
                    $subject = 'Пустая ссылка на лендинг';
                    $text = "<p>Billing Master обнаружил пустую ссылку на внешний лендинг у продукта $product_name</p><p>Проверьте настройки продукта.</p>";
                    $send_notif = Email::SendMessageToBlank($setting['admin_email'], 'BM', $subject, $text);
                    exit();
                }    
               
            }
            
        } else require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        
        exit();
        
    }


    /**
     * СТРАНИЦА ПАРТНЁРА В ЛК
     */
    public function actionAff()
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('partnership', 1);
        if(!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $title = 'Партнёрская программа';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        
        // Проверка авторизации
        $userId = User::checkLogged();
        
        // Данные юзера
        $user = User::getUserById($userId);
        if ($user['is_partner'] != 1) {
            System::redirectUrl('/lk');
        }
		
		if (isset($_POST['save_postback'])) {
            $postback = json_encode($_POST['postback']);
            $fb_pixel = isset($_POST['fb_pixel']) ? json_encode($_POST['fb_pixel']) : null;
            $add = Aff::writePostback($userId, $postback, $fb_pixel);
            if ($add) {
                System::redirectUrl('/lk/aff?success');
            }
        }
        
        if (isset($_POST['save_req'])) {
            if (isset($_POST['req'])) {
                foreach($_POST['req'] as $key => $value){
                    if ($key != 'rs') {
                        $req[$key] = htmlentities($value);
                    } else {
                        $req[$key]['rs'] = htmlentities($value['rs']);
                        $req[$key]['name'] = htmlentities($value['name']);
                        $req[$key]['bik'] = htmlentities($value['bik']);
                        $req[$key]['itn'] = htmlentities($value['itn']);
                    }
                }
                
                $req = serialize($req);
            } else {
                $req = null;
            }
            
            $addreq = Aff::UpdateReq($userId, $req);
            if($addreq) {
                System::redirectUrl('/lk/aff?success');
            }
        }
        
        if (isset($_POST['addlink']) && !empty($_POST['url'])) {
            $url = filter_var($_POST['url'], FILTER_SANITIZE_STRING);
            $desc = htmlentities($_POST['desc']);
            
            $addlink = Aff::AddPartnerShortLink($userId, $url, $desc);
            if ($addlink) {
                System::redirectUrl('/lk/aff?success');
            }
        }
        
        if (isset($_POST['deletelink'])) {
            $link_id = intval($_POST['link_id']);
            $del = Aff::deleteShortLink($link_id);
            if ($del) {
                System::redirectUrl('/lk/aff?success');
            }
        }
        
        $links = Aff::getPartnerLinks();
        $short_links = Aff::getShortLinkByPartner($userId);
        
        // настройки партнёрки
        $params = unserialize(System::getExtensionSetting('partnership'));
        
        $months = [
            "Декабрь", "Январь", "Февраль", "Март",
            "Апрель", "Май", "Июнь",
            "Июль", "Август", "Сентябрь",
            "Октябрь", "Ноябрь", "Декабрь"
        ];

        // Реквизиты партнёра
        $req = Aff::getPartnerReq($userId);
		$postbacks = json_decode($req['postbacks'], true);
        $fb_pixel = json_decode($req['fb_pixel'], true);
        $paid = isset($_GET['all']) ? null :1;
        $total = Aff::getUserTransactData($userId, 'aff'); // Всего заработано

        if (isset($params['params']['return_period']) && $params['params']['return_period'] > 0) {
            $date = time() - ($params['params']['return_period'] * 86400);
            $total2 = Aff::getUserTransactData($userId, 'aff', $date);
        }

        $hits = Aff::contHitsToPartner($userId);
        $last_pay = Aff::getParnerLastPay($userId, 'aff');
        $orderss = Aff::getHistoryTransaction($userId, 1, 'aff', $paid);
        $total_orders = Aff::CountOrdersToPartner($userId, 1, 1);
        $clients = Aff::getUserFromPartner($userId);
        $count_month_has_date = Aff::CountMonthHasDate($userId);
        $main_table = Aff::getDateForMainTable($userId);
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/aff/aff_index.php');
    }
    
    
    
    // СТРАНИЦА АВТОРА В ЛИЧНОМ КАБИНЕТЕ
    public function actionAuthor()
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('partnership', 1);
        if(!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        $title = 'Кабинет автора';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'lk';
        
        // Проверка авторизации
        $userId = User::checkLogged();
        
        // Данные юзера
        $user = User::getUserById($userId);
        if($user['is_author'] != 1){
            header("Location: ".$setting['script_url']."/lk");
        }
        
        if(isset($_POST['save_req'])){
            
            if(isset($_POST['req'])){
                
                foreach($_POST['req'] as $key => $value){
                    
                    if($key != 'rs') $req[$key] = htmlentities($value);
                    else {
                        $req[$key]['rs'] = htmlentities($value['rs']);
                        $req[$key]['name'] = htmlentities($value['name']);
                        $req[$key]['bik'] = htmlentities($value['bik']);
                        $req[$key]['itn'] = htmlentities($value['itn']);
                    }
                }
                
                //print_r($req);
                //exit;
                
                $req = serialize($req);
            } else $req = null;
            
            
            $addreq = Aff::UpdateReq($userId, $req);
            if($addreq) header("Location: ".$setting['script_url']."/lk/author?success");
            
        }
        
        // настройки партнёрки
        $params = unserialize(System::getExtensionSetting('partnership'));
        
        // Реквизиты партнёра
        $req = Aff::getPartnerReq($userId);
        
        $transacts = Aff::getAuthorTransaction($userId);
        $total = Aff::getUserTransactData($userId, 'author');
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/aff/author_index.php');
        return true;
    }
    
    
    
    
    // СТРАНИЦА ОПИСАНИЯ ПАРТНЁРКИ
    public function actionAffdesc()
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('partnership', 1);
        if(!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $title = 'Партнёрская программа';
        $meta_desc = isset($params['params']['metadesc']) ? $params['params']['metadesc'] : null;
        $meta_keys = isset($params['params']['metakeys']) ? $params['params']['metakeys'] : null;
        $use_css = 1;
        $is_page = 'aff';
        
        $params = unserialize(System::getExtensionSetting('partnership'));
        
        // ВЫЯСНЯЕМ ПОКАЗЫВАТЬ ЛИ ССЫЛКИ НА РЕГИСТРАЦИЮ И ЛК
        if(User::isAuth()){
            // Обновляем данные пользователя
            $user_id = $_SESSION['user'];
            $show_cabinet = false;
            // Данные юзера
            $user = User::getUserById($user_id);
            if($user['is_partner'] == 1){
                $show_aff = false;
            } else $show_aff = true;
        } else {
            $show_aff = true;
            $show_cabinet = true;
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/aff/index.php');
        return true;
    }
    
    
    
    // РЕГИСТРАЦИЯ В ПАРТНЁРКЕ
    public function actionAffreg()
    {
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        $extension = System::CheckExtensension('partnership', 1);
        if (!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit;
        }
        
        // Проверяем авторизацию юзера
        if (User::isAuth()) {
            // Обновляем данные пользователя
            $user_id = $_SESSION['user'];
            $verify = isset($_COOKIE["aff_$cookie"]) ? Aff::PartnerVerify(intval($_COOKIE["aff_$cookie"])) : false; // Проверка партнёра на существование и самозаказ
            $partner_id = $verify ? intval($_COOKIE["aff_$cookie"]) : 0;
           
            Aff::AddUserToPartner($user_id, $partner_id);
    
            $partner_group = Aff::getPartnerGroup();
            if ($partner_group) {
                User::WriteUserGroup($user_id, $partner_group);
            }
            
            // Перенаправляем в личный кабинет.
            header("Location: ".$setting['script_url']."/lk/aff?success_reg");
        }
        
        $title = 'Регистрация в партнёрской программе';
        $meta_desc = '';
        $meta_keys = '';
        $use_css = 1;
        $is_page = 'aff';
        $date = time();
        
        if (isset($_POST['affreg']) && !empty($_POST['email']) && !empty($_POST['pass'])) {
            $name = htmlentities($_POST['name']);
            $timeout = intval($_POST['tm']); // время заполнения формы
            $now = time();
            
            if (($now - $timeout) < 4) {
                exit('Ошибка 344 - afftime');
            }
            
            $email = htmlentities($_POST['email']);
            $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $about = htmlentities($_POST['about']);
            $reg_key = md5($date);
            $param = isset($_COOKIE["$cookie"]) ? htmlentities($_COOKIE["$cookie"]) : htmlentities($_SESSION["$cookie"]);
            
            // Партнёрка
            $verify = isset($_COOKIE["aff_$cookie"]) ? Aff::PartnerVerify(intval($_COOKIE["aff_$cookie"])) : false; // Проверка партнёра на существование и самозаказ
            $partner_id = $verify && $verify['email'] != $email ? intval($_COOKIE["aff_$cookie"]) : 0;
    
            $partner_group = Aff::getPartnerGroup();
            $add = Aff::AddNewPartner($name, $email, $pass, $about, $param, $date, $reg_key, $partner_group, $partner_id);
            
            if ($add) {
                // Отправить партнёру письмо с подтверждением
                $send = Email::SendPernerLetter($name, $email, $reg_key);
                $message = '<h4>Регистрация прошла успешно<br />Вам на почту отправлено письмо для подтверждения.<br />На всякий случай проверьте папку спам.</h4>';
            } else {
                $message = '<h4>Такой e-mail уже зарегистрирован, войдите на сайт используя ваш e-mail и пароль</h4>';
            }
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/aff/reg.php');
        
        return true;
    }
    
    
    
    
    
    // ПОДТВЕРЖДЕНИЕ ЕМЕЙЛА ПРИ РЕГИСТРАЦИИ ПАРТНЁРА
    public function actionConfirm()
    {
        $setting = System::getSetting();
        $extension = System::CheckExtensension('partnership', 1);
        if(!$extension) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        if(isset($_GET['key'])){
            $key = htmlentities($_GET['key']);
            
            // Найти юзера с этим ключом и изменить статус на 1.
            $user = User::getUserDataToRegkey($key);
            if($user){
                User::updateUserStatus($user['user_id'], 1);
            } else exit('Ошибка, наверное, вы уже подтвердили ваш e-mail');
            
            // Вывести сообщение что емейл подтверждён.
            $title = 'Ваш e-mail подтверждён';
            $meta_desc = '';
            $meta_keys = '';
            $use_css = 1;
            $is_page = 'aff';
            
            // написать админу что зарегался новый партнёр
            Email::SendNotifAboutPartnerToAdmin($user['user_name'], $user['email']);
        
            require_once (ROOT . '/template/'.$setting['template'].'/views/aff/confirm.php');
            return true;
            
        } else {
            header("Location: ".$setting['script_url']);
        }
    }
    
    
    
    // РЕДИРЕКТЫ ПАРТНЁРОВ
    public function actionRedirect($id)
    {
        $id = intval($id);
        $url = Aff::getAffRedirect($id);
        $setting = System::getSetting();
        $aff_set = unserialize(System::getExtensionSetting('partnership'));
        $aff_life = intval($aff_set['params']['aff_life']);
        
        if($url){
            $cookie = $setting['cookie'];
            
            $hit = Aff::AffHits($url['partner_id']);
            if($hit){
            setcookie("aff_$cookie", $url['partner_id'], time()+3600*24 * $aff_life, '/');
            header("Location: ".$url['url']);
            }
            
            exit();
            
        } else require_once (ROOT . '/template/'.$setting['template'].'/404.php');
    }
    
    
}