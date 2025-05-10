<?php defined('BILLINGMASTER') or die;

class adminSettingController extends AdminBase {
    
    
    public function actionSettings()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_main_tunes'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();
        $params = json_decode($setting['params'], true);
        $socbut = unserialize(base64_decode($setting['socbut']));
        $smsс = unserialize(base64_decode($setting['smsc']));
        $ticket = unserialize(base64_decode($setting['org_data']));
        $remind_sms1 = unserialize(base64_decode($setting['remind_sms1']));
        $remind_sms2 = unserialize(base64_decode($setting['remind_sms2']));

        $folder = ROOT . '/template/' . $setting['template'] . '/js/'; // папка с плеером
        $path_orig_player = $folder . 'player_bm_orig.js'; // Полный путь с именем файла
        $path_cur_player = $folder . 'player_bm.js'; // Полный путь с именем файла
        if (file_exists($path_orig_player) && file_exists($path_cur_player)) {
            $diffplayer = md5_file($path_orig_player) != md5_file($path_cur_player);
        }

        if (isset($_GET['resetplayer']) && $_GET['token'] == $_SESSION['admin_token']) {
            $copy = copy($path_orig_player, $path_cur_player);
            if($copy) {
                header("Location: ".$setting['script_url']."/admin/settings?success");
            }
        }

        if (isset($_POST['save_main']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_main_tunes'])){
                System::redirectUrl("/admin/settings");
                exit();
            }

            $site_name = $_POST['site_name'];
            $admin_email = trim($_POST['admin_email']);
            $support_email = trim($_POST['support_email']);
            $lang = $_POST['lang'];
            $currency = $_POST['currency'];
            $template = $_POST['template'];
            $template_set = $_POST['template_set'];
            $show_items = $_POST['show_items'];
            $script_url = trim($_POST['script_url']);
            $security_key = trim($_POST['security_key']);
            $cookie = $_POST['cookie'];
            $secret_key = trim($_POST['secret_key']);
			$private_key = trim($_POST['private_key']);
            $debug_mode = $_POST['debug_mode'];
            $max_upload = $_POST['max_upload'] < 32768 ? intval($_POST['max_upload'])  : 32767;
            $login_redirect = intval($_POST['login_redirect']);
            
            $params = json_encode($_POST['params']);
            
            $use_cart = intval($_POST['use_cart']);
            $enable_catalog = intval($_POST['enable_catalog']);
            $enable_reviews = intval($_POST['enable_reviews']);
            $enable_landing = intval($_POST['enable_landing']);
            $enable_sale = intval($_POST['enable_sale']);
            $enable_cabinet = intval($_POST['enable_cabinet']);
            $enable_registration = intval($_POST['enable_registration']);
            $enable_feedback = intval($_POST['enable_feedback']);
            $write_feedback = intval($_POST['write_feedback']);
            $split_test_enable = intval($_POST['split_test_enable']);
            $request_phone = $_POST['request_phone'];
            $show_order_note = intval($_POST['show_order_note']);
            $email_protection = intval($_POST['email_protection']);
            $strict_report = intval($_POST['strict_report']);
            $simple_free_dwl = $_POST['simple_free_dwl'];   
            $dwl_in_lk = $_POST['dwl_in_lk'];
            
            $order_life_time = $_POST['order_life_time'] < 128 ? $_POST['order_life_time'] : 127;
            $dwl_time = $_POST['dwl_time'] < 32768 ? intval($_POST['dwl_time'])  : 32767;
            $dwl_count = $_POST['dwl_count'] < 128 ? intval($_POST['dwl_count'])  : 127;
            
            $yacounter = htmlentities($_POST['yacounter']);
            $ga_target = intval($_POST['ga_target']);
            
            $use_smtp = $_POST['use_smtp'];
            $smtp_host = $_POST['smtp_host'];
            $smtp_port = $_POST['smtp_port'];
            $smtp_user = $_POST['smtp_user'];
            $smtp_pass = trim($_POST['smtp_pass']);
            $smtp_ssl = $_POST['smtp_ssl'];
            $sender_name = $_POST['sender_name'];
            $sender_email = trim($_POST['sender_email']);
            $smtp_domain = $_POST['smtp_domain'];
            $smtp_selector = $_POST['smtp_selector'];
            $smtp_private_key = trim($_POST['smtp_private_key']);
            $return_path = $_POST['return_path'];
            
            $show_surname = intval($_POST['show_surname']);
            $show_telegram_nick = intval($_POST['show_telegram_nick']);
            $show_instagram_nick = intval($_POST['show_instagram_nick']);
            
            $smsc = base64_encode(serialize($_POST['smsc']));
            $countries_list = !empty($_POST['countries_list']) ? json_encode($_POST['countries_list']) : '';
            $session_time = intval($_POST['session_time']) < 128 ? intval($_POST['session_time']) : 127;
            $editor = (int)$_POST['editor'];
            $logs_life_time = (int)$_POST['logs_life_time'];

            if (isset($_FILES["cover"]["tmp_name"]) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке 
                
                $folder = ROOT . '/images/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            } else {
                $img = $_POST['current_img'];
            }

            if (isset($_FILES["favicon"]["tmp_name"]) && $_FILES["favicon"]["size"] != 0) {
                $icon_types = array(
                    'image/vnd.microsoft.icon',
                    'image/x-icon',
                    'image/x-ms-bmp');

                $tmp_name_favicon = $_FILES["favicon"]["tmp_name"]; // Временное имя картинки на сервере
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if (in_array(finfo_file($finfo, $tmp_name_favicon), $icon_types)) {
                    finfo_close($finfo);
                    $path = ROOT . '/favicon.ico';
                    move_uploaded_file($tmp_name_favicon, $path);
                }
            }

            if (isset($_FILES["playerjs"]["tmp_name"]) && $_FILES["playerjs"]["size"] != 0) {
                $playerjs_upload = $_FILES["playerjs"]["tmp_name"];
                move_uploaded_file($playerjs_upload, $path_cur_player);
            }
            
            $save = System::saveSettings($site_name, $admin_email, $support_email, $lang, $currency, $template, $template_set,
                $show_items, $script_url, $security_key, $cookie, $secret_key, $debug_mode, $max_upload, $use_cart, $enable_catalog,
                $enable_reviews, $enable_landing, $enable_sale, $enable_cabinet, $enable_registration, $enable_feedback, $write_feedback,
                $split_test_enable, $request_phone, $show_order_note, $email_protection, $strict_report, $simple_free_dwl, $dwl_in_lk,
                $order_life_time, $dwl_time, $dwl_count, $yacounter, $ga_target, $use_smtp, $smtp_host, $smtp_port, $smtp_user, $smtp_pass,
                $smtp_ssl, $sender_name, $sender_email, $smtp_domain, $smtp_selector, $smtp_private_key, $img, $return_path, $login_redirect,
                $show_surname, $show_telegram_nick, $show_instagram_nick, $smsc, $countries_list, $session_time, $private_key, $params, $editor,
                $logs_life_time
            );

            if($save) {
                header("Location: ".$setting['script_url']."/admin/settings?success");
            }
        }
        
        
        if (isset($_POST['save_vid']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin/settings/");
                exit();
            }

            $slogan = $_POST['slogan'];
            $phone = $_POST['phone'];
            $phone_link = $_POST['phone_link'];
            $counters = $_POST['counters'];
            $counters_head = $_POST['counters_head'];
            $logotype = $_POST['logotype'];
            $copyright = $_POST['copyright'];
            $fix_head = intval($_POST['fix_head']);
			$sidebar = htmlentities($_POST['sidebar']);
            $socbut = base64_encode(serialize($_POST['socbut']));
			
			$external_url = htmlentities($_POST['external_url']);
            
            $main_page_content = $_POST['main_page_content'];
            $main_page_title = $_POST['main_page_title'];
            $main_page_desc = $_POST['main_page_desc'];
            $main_page_keys = $_POST['main_page_keys'];
            $main_page_tmpl = isset($_POST['main_page_tmpl']) ? $_POST['main_page_tmpl'] : 1;
            $main_page_text = $_POST['main_page_text'];
            $in_head = $_POST['in_head'];
            $in_body = $_POST['in_body'];

            $catalog_title = htmlentities($_POST['catalog_title']);
            $catalog_h1 = htmlentities($_POST['catalog_h1']);
            $catalog_desc = htmlentities($_POST['catalog_desc']);
            $catalog_keys = htmlentities($_POST['catalog_keys']);
            
            $reviews_tune = base64_encode(serialize($_POST['reviews_tune']));
            
            $politika_link = htmlentities($_POST['politika_link']);
            $oferta_link = htmlentities($_POST['oferta_link']);
            $politika_text = $_POST['politika_text'];
            $oferta_text = $_POST['oferta_text'];
			
			$custom_css = $_POST['custom_css'];
            
            $save = System::SaveVID($slogan, $phone, $phone_link, $counters, $counters_head, $logotype, $copyright, $socbut, $main_page_content, $main_page_title, 
            $main_page_desc, $main_page_keys, $main_page_tmpl, $main_page_text, $in_head, $in_body, $catalog_title, $catalog_h1, $catalog_desc, $catalog_keys,
            $reviews_tune, $politika_link, $oferta_link, $politika_text, $oferta_text, $fix_head, $external_url, $sidebar, $custom_css );
            
            if ($save) {
                header("Location: ".$setting['script_url']."/admin/settings?cat=vid&success");
            }
        }
        
        
        if (isset($_POST['save_letters']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_main_tunes'])) {
                header("Location: /admin/settings");
                exit();
            }
            
            $client_letter_subj = $_POST['client_letter_subj'];
            $client_letter = $_POST['client_letter'];

            $reg_confirm_letter = $_POST['reg_confirm_letter'];
            $register_letter = $_POST['register_letter'];
            $pass_reset_letter = $_POST['pass_reset_letter'];

            $remind_letter1 = base64_encode(serialize($_POST['remind_letter1']));
            $remind_letter2 = base64_encode(serialize($_POST['remind_letter2']));
            $remind_letter3 = base64_encode(serialize($_POST['remind_letter3']));
            
            $remind_sms1 = base64_encode(serialize($_POST['remind_sms1']));
            $remind_sms2 = base64_encode(serialize($_POST['remind_sms2']));
            $reg_sms = json_encode($_POST['reg_sms']);
            $ticket = base64_encode(serialize($_POST['ticket']));
            
            $save = System::saveLetters($client_letter_subj, $client_letter, $reg_confirm_letter, $register_letter,
                $pass_reset_letter, $remind_letter1, $remind_letter2, $remind_letter3, $remind_sms1, $remind_sms2,
                $reg_sms, $ticket);

            if ($save) {
                System::redirectUrl('/admin/settings?cat=letters&success');
            }
        }
        
        
        if(isset($_POST['email_test']) && !empty($_POST['email_for_test']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            // проверить заполнение админсокго емейла + отправителя
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin/settings");
                exit();
            }
            $error = null;
            if(empty($setting['admin_email'])) $error = 'Не забудьте указать email администратора<br />';
            if(empty($setting['support_email'])) $error .= 'Не забудьте указать email техподдержки<br />';
            
            if(empty($setting['sender_email'])) $error .= 'Не забудьте указать email отправителя (вкладка Почта)<br />';
            if(empty($setting['sender_name'])) $error .= 'Не забудьте указать имя отправителя (вкладка Почта)';
            
                
            // делаем отправку
            
            $email = trim(mb_strtolower($_POST['email_for_test']));
            $name = '';
            $subject = '# Тест отправки почты';
            $text = '<div style="width:100%; margin:0; padding:1em 0; background:#373A4C">
            <div style="width:80%; margin:1em auto; padding:1em 5%; background:#fff">
                <h1>Тестирование отправки почты</h1>
                <p>Здравствуйте! </p>
                <p>Если вы получили это письмо, значит почта на вашем сайте работает исправно.<br />
                Отвечать на это сообщение не нужно.</p>
                <p>Желаем успешной и продуктивной работы!<br />С уважением, команда Billing Master.</p>
            </div>
            </div>';
            $send = Email::SendMessageToBlank($email, $name, $subject, $text);
            
            require_once (ROOT . '/template/admin/views/settings/email_test.php');
            return true;
                
            
        }

        if (isset($_GET['cat'])) {
            if($_GET['cat'] == 'vid') {
                require_once (ROOT . '/template/admin/views/settings/index_vid.php');
            } elseif($_GET['cat'] == 'letters') {
                require_once (ROOT . '/template/admin/views/settings/index_letters.php');
            }
        } else {
            require_once (ROOT . '/template/admin/views/settings/index.php');
        }
    }
    
    
	
	// ВЫВОД КОНФИГА
    public function actionConfig() // admin/config
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_main_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        $paramPath = ROOT . '/config/config.php';
        $params = include($paramPath);
        
        echo '<p><br />DB name: '.$dbname;
        echo '<br />preffix: '.$prefics;
        echo '<br />user: '.$user;
        echo '<br />pass: '.$password;
		echo '<br /><br /><< <a href="/admin">Dashboard</a></p>';
        echo phpinfo();
    }
	
	
    
    // ВЫВОД ЗАДАНИЙ КРОН
    public static function actionCronjobs()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_main_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        require_once (ROOT . '/template/admin/views/settings/cron.php');
    }
    
    
    
    // СПОСОБЫ ДОСТАВКИ
    public function actionDeliveryset()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        
        $velivery_methods = Order::getDeliveryMethods(0);
        require_once (ROOT . '/template/admin/views/settings/delivery_var_list.php');
    }
    
    
    // ДОБАВИТЬ СПОСОБ ДОСТАВКИ
    public function actionAdddeliverymethod()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        
        if(isset($_POST['addmethod']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $ship_desc = htmlentities($_POST['ship_desc']);
            $status = intval($_POST['status']);
            $tax = intval($_POST['tax']);
            
            $add = System::addDeliveryMethod($name, $ship_desc, $status, $tax);
            if($add) header("Location: /admin/deliverysettings");
        }
        
        require_once (ROOT . '/template/admin/views/settings/delivery_var_add.php');
    }
    
    
    // ИЗМЕНИТЬ СПОСОБ ДОСТАВКИ
    public function actionEditdeliverymethod($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if(isset($_POST['editmethod']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $ship_desc = htmlentities($_POST['ship_desc']);
            $status = intval($_POST['status']);
            $tax = intval($_POST['tax']);
            
            $edit = System::editDeliveryMethod($id, $name, $ship_desc, $status, $tax);
            if($edit) header("Location: /admin/deliverysettings/edit/$id");
            
            
        }
        
        $ship_method = System::getShipMethod($id);
        
        require_once (ROOT . '/template/admin/views/settings/delivery_var_edit.php');
    }
    
    
    // УДАЛИТЬ СПОСОБ ДОСТАВКИ
    public function actionDeletedeliverymethod($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) {
            header("Location: /admin");
            exit();
        }
        $name = $_SESSION['admin_name'];
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $del = System::deleteShipMethod($id);
            if($del) header("Location: ".$setting['script_url']."/admin/deliverysettings?success");
            else header("Location: ".$setting['script_url']."/admin/deliverysettings?fail");
            
        }
    }
    
    
    // СПИСОК ПРАВ МЕНЕДЖЕРОВ
    public function actionPermissions()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_perms'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        
        $levels = System::getACLlist();
        require_once (ROOT . '/template/admin/views/settings/acl.php');
    }
    
    
    // ДОБАВИТЬ ПРАВА МЕНЕДЖЕРА
    public function actionAddpermissions()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_perms'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['addperm'])&& isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $user_id = intval($_POST['user_id']);
            $perm = serialize($_POST['perm']);
            
            $write = System::AddPermiss($user_id, $perm);
            if($write) header("Location: /admin/permissions?success");
            
        }
        
        $levels = System::getACLlist();
        require_once (ROOT . '/template/admin/views/settings/acl_add.php');
    }
    
    
    // ИЗМЕНИТЬ ПРАВА МЕНЕДЖЕРА
    public function actionEditpermissions($id)
    {
        $acl = $acl = self::checkAdmin();
        if(!isset($acl['show_perms'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['saveperm'])&& isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $perm = serialize($_POST['perm']);
            
            $upd = System::UpdPermiss($id, $perm);
            if($upd) header("Location: /admin/permissions/edit/$id?success");
            
        }
        
        $level = System::getACLbyID($id);
        require_once (ROOT . '/template/admin/views/settings/acl_edit.php');
    }
    
    
    // УДАЛИТЬ ПРАВА МЕНЕДЖЕРА
    public function actionDelpermissions($id)
    {
        $acl = $acl = self::checkAdmin();
        if(!isset($acl['show_perms'])) {
            header("Location: /admin");
            exit();   
        }
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $del = System::delACL($id);
            if($del) header("Location: ".$setting['script_url']."/admin/permissions");
        }
        
    }
    
    
    // НАСТРОЙКА ПЛАТЁЖЕК
    public function actionPayments()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['install_payment']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            
            if(isset($_FILES["payment"]["tmp_name"]) && $_FILES["payment"]["size"] != 0 ){
                
                $tmp_name = $_FILES["payment"]["tmp_name"];
                $name = $_FILES["payment"]["name"];
                $dir = time();
                $tmp_path = ROOT . "/tmp/$dir";
                $template = $setting['template'];
                
                $zip = new ZipArchive(); //Создаём объект для работы с ZIP-архивами
                
                //Открываем архив archive.zip и делаем проверку успешности открытия
                if ($zip->open($tmp_name) === true) {
                    
                    $zip->extractTo($tmp_path); //Извлекаем файлы в указанную директорию
                    $zip->close(); //Завершаем работу с архивом
                    $message = '<div class="admin_message">Расширение успешно установлено</div>';
                } else {
                    $message = '<div class="admin_warning">Ошибка при установке</div>';
                }
                
                // Подключить файл params.php
                if(include (ROOT . "/tmp/$dir/install.php")){
                    
                    // Сделать запись в БД
                    $install = System::installPayment($name, $title, $enable, $params, $desc);
                    if($install){
                            // Создать папки
                        if($folders != false){
                            foreach($folders as $folder){
                                $path = ROOT.'/'.$folder[0];
                                mkdir($path);
                            }
                        }
                        // Переместить файлы согласно инструкции в install.php
                        foreach($files as $file){
                            $old = $file[0];
                            $new = $file[1];
                            rename($tmp_path.$old, $new);
                        }
                        
                    } else {
                        $message = '<div class="admin_warning">Расширение уже установлено</div>';
                    }
                    
                    
                } else {
                    $message = '<div class="admin_warning">Не найден файл установки</div>';
                }
                
            }
        }
        
        $payments = Order::getPaymentsForAdmin();
        require_once (ROOT . '/template/admin/views/settings/payments.php');
    }
    
    
    
    public function actionEditpayments($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        
        if(isset($_POST['savepayments']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            
            $title = $_POST['title'];
            $public_title = $_POST['public_title'];
            $sort = $_POST['sort'];
            $status = $_POST['status'];
            $payment_desc = $_POST['payment_desc'];
            $params = base64_encode(serialize($_POST['params']));
            
            $edit = Order::EditPayments($id, $title, $public_title, $sort, $status, $payment_desc, $params);
            if($edit) header("Location: ".$setting['script_url']."/admin/paysettings/$id?success");
            
        }
        
        $payment = Order::getPaymentDataForAdmin($id);
        
        require_once (ROOT . '/template/admin/views/settings/edit_payment.php');
    }
    
    
    
    
    public function actionDeletepayments($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_payment_tunes'])) {
            header("Location: /admin");
            exit();
        }
        $name = $_SESSION['admin_name'];
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $del = System::deletePayment($id);
            if($del) header("Location: ".$setting['script_url']."/admin/paysettings?success");
            else header("Location: ".$setting['script_url']."/admin/paysettings?success");
            
        }
    }


    // ОБНОВИТЬ CMS
    public function actionCMSUpdate()
    {
        if (isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $acl = self::checkAdmin();
            if (!isset($acl['show_ext_tunes']) || !isset($acl['change_main_tunes'])) {
                header("Location: /admin");
                exit();
            }

            $res = System::curl('https://lk.school-master.ru/cmsupdate.php', array('key' => 'flS16H5PgjcI'));
            if ($res['info']['http_code'] != 200 || !strlen($res['content'])) {
                System::addError('Ошибка при загрузке обновления');
                exit;
            }

            $tmp_path = ROOT . '/tmp/' . time();
            if (file_exists($tmp_path)) {
                System::removeDirectory($tmp_path);
            }

            mkdir($tmp_path);
            $tmp_zip =  $tmp_path . '/updatecms.zip';
            file_put_contents($tmp_zip, $res['content']);

            $result = System::installExtensions($tmp_zip, 'updatecms.zip', 'update');

            System::hasSuccess() ? System::showSuccess() : System::showError();
            exit;
        }
    }
    
    // ПОЛУЧИТЬ СПИСОК РАСШИРЕНИЙ
    public function actionExtensions()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_ext_tunes'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        if (isset($_POST['install_ext']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_main_tunes'])) {
                header("Location: /admin");
                exit();
            }

            // Переместить архив в папку tmp
            // Распаковать архив
            if(isset($_FILES["extens"]["tmp_name"]) && $_FILES["extens"]["size"] != 0 ) {
                $tmp_name = $_FILES["extens"]["tmp_name"];
                $name = $_FILES["extens"]["name"];
                $result = System::installExtensions($tmp_name, $name);
            }
        }

        $exts = System::getAllExtensions('system');
        require_once (ROOT . '/template/admin/views/settings/extensions.php');
    }
    
    
    // СПИСОК ВСЕХ РАСШИРЕНИЙ
    public function actionAllextensions()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_ext_tunes'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $system = 'all';
        $exts = System::getAllExtensions('all');
        
        echo '<p>Список установленных расширений</p><p>';
        if($exts){
            foreach($exts as $item){
                echo $item['name'].' - '.$item['title']. ' - '.$item['type'].'<br />';
            }
        }
        echo '</p>';
    }
    
    
    
    // УДАЛЕНИЕ ОБЛОЖЕК 
    public function actionDelimg($id)
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['del_img']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_products'])){
                header("Location: /admin");
                exit();
            }
            $file = $_POST['path'];
            $page = $_POST['page'];
            $table = htmlentities($_POST['table']);
            $name = $_POST['name'];
            $where = $_POST['where'];
            
            // Удалить файл физически, пока убрали до внедрения нормального файл-менеджера во всех местах
            // Потому-что при копированни потом когда люди хотят изменить картинку она удалется и из первичного элемента
            // с которого происходит копирование
            // if(file_exists($file)){
            //    unlink($file);   
            //}
            
                // Удалить из БД
                $db = Db::getConnection();  
                $sql = 'UPDATE '.PREFICS.$table.' SET '.$name.' = "" WHERE '.$where.' = '.$id;
                $result = $db->prepare($sql);
                $result->bindParam(':name', $name, PDO::PARAM_STR);
                $result->bindParam(':title', $title, PDO::PARAM_STR);
                $result->bindParam(':alias', $alias, PDO::PARAM_STR);
                if($result->execute()){
                    header("Location: /$page?success");
                } else exit('Error delete image');
                }
            
    }
    
    
    
    // BACKUPS
    public static function actionBackup()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_backups'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_GET['file'])){
            header("Location: /tmp/".$_GET['file']);
        }
        
        if(isset($_POST['backup']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $backup = System::createBackup();
            if($backup) header("Location: ".$setting['script_url']."/admin/backup?success&file=$backup" );
            else echo 'Error!';
            
        }
        
        require_once (ROOT . '/template/admin/views/settings/backup.php');
    }
    
    
    
    
    // ПУНКТЫ МЕНЮ
    public function actionMenuitems()
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();
        $menu_items = System::getMenuItems();
        $user_menu = json_decode($setting_main['user_menu'], 1);
        
        if(isset($_POST['user_menu_save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            $user_menu = json_encode($_POST['user_menu']);
            $save = System::saveUserMenu($user_menu);
            if($save) header("Location: /admin/menuitems?success");
        }
        
        require_once (ROOT . '/template/admin/views/settings/menuitems.php');
    }
    
    
    // СОЗДАТЬ ПУНКТ МЕНЮ
    public function actionAddmenuitem()
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $menu_items = System::getMenuItems();
        if(isset($_GET['type'])) $type = htmlentities($_GET['type']);
        
        if(isset($_POST['addmenuitem']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $parent_id = intval($_POST['parent_id']);
            $url = $_POST['url'];
            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $type = htmlentities($_POST['type']);
            $menu_id = intval($_POST['menu_id']);
            $title = htmlentities($_POST['title']);
            $new_window = intval($_POST['new_window']);
            $sitemap = intval($_POST['sitemap']);
            $changefreq = htmlentities($_POST['changefreq']);
            $visible = intval($_POST['visible']);
            $priority = htmlentities($_POST['priority']);
			
            $add = System::addMenuItem($name, $url, $sort, $status, $type, $menu_id, $title, $new_window, $parent_id, $sitemap, 
            $changefreq, $visible, $priority);
            if($add) header("Location: ".$setting['script_url']."/admin/menuitems?success");
            
        }
        
        require_once (ROOT . '/template/admin/views/settings/addmenuitem.php');
    }
    
    
    // ИЗМЕНИТЬ ПУНКТ МЕНЮ
    public function actionEditmenuitem($id)
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $menu_items = System::getMenuItems();
        $type = isset($_GET['type']) ? htmlentities($_GET['type']) : null;

        if (isset($_POST['savemenuitem']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_main_tunes'])) {
                System::redirectUrl('/admin');
            }

            $name = htmlentities($_POST['name']);
            $parent_id = intval($_POST['parent_id']);
            $url = $_POST['url'];
            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $menu_id = intval($_POST['menu_id']);
            $title = htmlentities($_POST['title']);
            $new_window = intval($_POST['new_window']);
            $sitemap = intval($_POST['sitemap']);
            $changefreq = htmlentities($_POST['changefreq']);
            $visible = intval($_POST['visible']);
            $priority = htmlentities($_POST['priority']);

            $edit = System::editMenuItem($id, $name, $url, $sort, $status, $menu_id, $title, $new_window, $parent_id,
                $sitemap, $changefreq, $visible, $priority
            );

            if ($edit) {
                if (isset($_POST['training'])) {
                    $tr_save = Training::SaveMPSettings($_POST['training']['params']);
                }

                System::redirectUrl("/admin/menuitems/edit/$id?type=$type&success");
            }
        }

        $item = System::getMenuItem($id);

        require_once (ROOT . '/template/admin/views/settings/editmenuitem.php');
    }
    
    
    // УДАЛИТЬ ПУНКТ МЕНЮ
    public function actionDelmenuitem($id)
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_main_tunes'])){
                header("Location: /admin");
                exit();
            }
            $del = System::delMenuItem($id);
            if($del) header("Location: ".$setting['script_url']."/admin/menuitems?success");
            else header("Location: ".$setting['script_url']."/admin/menuitems?fail");
        }
    }
    
    
    
    // ДЛЯ ТЕСТА И СИСТЕМНЫХ ОПЕРАЦИЙ
    public function actionTest()
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        //echo 'Test';
        
        // НАДО найти все номера начинающися с 9 и длинной 10 символов. 
        // типа  9095020747 
        // им нужно добавить +7
        // загвоздка только в регулярном выражении для поиска.


        System::correctionWrongPhones();
        echo System::getWrongPhonesCount();exit;

        //System::deleteDoubleLessonMap();exit;

        /*
        $phones = System::getWrongPhones();

        if (!empty($phones)) {
            foreach($phones as $user){

                // добавить +7
                // обновить запись в БД

                echo $user['user_name'].' = '.$user['phone'].' length: ' . strlen($user['phone']) . '<br />';
            }
        }
        */

        
        
        
        //require_once (ROOT . '/template/admin/views/-makeup.php');
        
    }
    
}