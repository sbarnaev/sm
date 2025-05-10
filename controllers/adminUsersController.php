<?php defined('BILLINGMASTER') or die;

class adminUsersController extends AdminBase {
    
    
    // Список юзеров
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_users'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $email = null;
        $user_group = null;
        $is_pagination = false;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = 3000;

        if (isset($_GET['role'])) {
            $role = htmlentities($_GET['role']);
        } else {
            $role = 0;
            $is_pagination = true;
        }
        
        if (isset($_POST['filter'])) {
            $email = isset($_POST['email']) ? htmlentities(trim($_POST['email'])) : null;
            $user_group = $_POST['user_group'] != 'without' ? intval($_POST['user_group']) : false;
            $is_pagination = false;
        }

        /**
         *  ПАГИНАЦИЯ
         */
        if ($is_pagination) {
            $total_users = User::countUsers();
            $pagination = new Pagination($total_users, $page, $setting['show_items']);
            $limit = $setting['show_items'];
        }

        $_POST['email'] = isset($_POST['email']) ? htmlentities(trim($_POST['email'])) : '';
        $users = User::getUserListForAdmin($role, $page, $limit, $user_group, $_POST);
        
        require_once (ROOT . '/template/admin/views/users/index.php');
    }
    
    
    
    
    // ИМПОРТ ЮЗЕРОВ
    public function actionImport()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_users'])) {
            header("Location: /admin");
        }
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $time = time();

        if ((!isset($_POST['import']) || $_POST['is_new']) && isset($_SESSION['import_data'])) {
            unset($_SESSION['import_data']);
        }

        if (isset($_POST['import']) && isset($_FILES['file'])&& $_FILES['file']['size'] != 0) {
            if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['admin_token'] || !isset($acl['export_users'])) {
                exit(json_encode(['redirect' => '/admin']));
            }

            if (!isset($_SESSION['import_data'])) {
                $_SESSION['import_data'] = [];
                exit(json_encode(['show_progress_bar' => true]));
            }

            if (ini_get("max_execution_time") < 180) {
                ini_set("max_execution_time", 180); //увеличить время скрипта
            }

            $field_1 = htmlentities($_POST['first_field']);
            $field_2 = htmlentities($_POST['second_field']);
            $field_3 = htmlentities($_POST['third_field']);
            $field_4 = htmlentities($_POST['fourth_field']);
            $field_5 = htmlentities($_POST['five_field']);
            
            $separator = htmlentities($_POST['separator']);
            $send_letter = intval($_POST['send_letter']);
            $letter = $_POST['letter'];
            $empty_name = htmlentities($_POST['empty_name']);
            $responder = intval($_POST['responder']);
            $validate = intval($_POST['validate']);
            $is_client = isset($_POST['is_client']) ? intval($_POST['is_client']) : 0;
            $is_partner = isset($_POST['is_partner']) ? intval($_POST['is_partner']) : 0;
            $is_subs = isset($_POST['is_subs']) ? intval($_POST['is_subs']) : 0;
            $groups = isset($_POST['groups']) ? $_POST['groups'] : false;
            
            $email = $phone = $user_name = $surname = $city = null;
            $lines = array();

            $file_path = isset($_SESSION['import_data']['file_path']) ? $_SESSION['import_data']['file_path'] : null;
            if (!$file_path && isset($_FILES['file'])) {
                $tmp_name = $_FILES["file"]["tmp_name"]; // Временное имя файла на сервере
                $pathinfo = pathinfo($_FILES["file"]["name"]);
                $file_name = time() . ".{$pathinfo['filename']}.{$pathinfo['extension']}"; // Имя файла для импорта
                $file_path = ROOT . "/tmp/$file_name"; // Путь для сохранения

                if (is_uploaded_file($tmp_name)) {
                    $content = file_get_contents($tmp_name);
                    if (!mb_check_encoding($content, 'UTF-8')) {
                        if (mb_check_encoding($content, 'cp1251')) {
                            $content = iconv('cp1251', 'UTF-8', $content);
                        } else {
                            unset($_SESSION['import_data']);
                            exit(json_encode(['redirect' => '/admin/users/import?fail']));
                        }
                    }

                    file_put_contents($file_path, $content);
                }
            }

            $wrong_email = isset($_SESSION['import_data']['wrong']) ? $_SESSION['import_data']['wrong'] : 0;
            $success = isset($_SESSION['import_data']['success']) ? $_SESSION['import_data']['success'] : 0;
            $emails = isset($_SESSION['import_emails']['emails']) ? $_SESSION['import_emails'] : array();
            $dupl_emails = isset($_SESSION['import_data']['dupl']) ? $_SESSION['import_data']['dupl'] : 0;

            $file = file($file_path);
            $filesize = sizeof($file);
            $max_users = $filesize >= 500 ? 50 : 25;
            $start = isset($_SESSION['import_data']['finish']) ? $_SESSION['import_data']['finish'] : 0;
            $finish = ($start + $max_users) < $filesize ? $start + $max_users : $filesize;
            $progress = 0;

            for ($str = $start; $str < $finish; $str++) {
                $line = $file[$str];
                if (empty($line)) {
                    break;
                }
                
                if ($field_2 == 'none') {
                    $lines[0] = trim($line);
                } else {
                    $lines = explode($separator, $line);
                }

                for ($i = 0; $i < 5; $i++) {
                    if (isset($lines[$i])) {
                        $field_name = 'field_'.($i+1);
                        switch($$field_name){
                            case 'email':
                                $email = trim($lines[$i]);
                                break;
                            case 'name':
                                $user_name = trim($lines[$i]);
                                break;
                            case 'phone':
                                $phone = trim($lines[$i]);
                                break;
                            case 'surname':
                                $surname = trim($lines[$i]);
                                break;
                            case 'city':
                                $city = trim($lines[$i]);
                                break;
                        }
                    }
                }


                if (!empty($emails) && in_array($email, $emails)) {
                    $dupl_emails++;
                    $wrong_email++;
                } else {
                    $emails[] = $email;
                    $subs_key = md5($email . $time);
                    $user_param = "$time;0;0;";

                    $add = User::importUsers($user_name, $email, $phone, $send_letter, $subs_key, $user_param, $setting,
                        $empty_name, $letter, $responder, $time, $groups, $validate, $is_client, $surname, $is_subs, $city
                    );

                    if ($add) {
                        $success++;

                        if ($is_partner) {
                            $act = Aff::AddUserToPartner($add['user_id'], 0);
                        }
                    } else {
                        $wrong_email++;
                    }
                }

                $progress = intval(($wrong_email + $success) / $filesize * 100);
            }

            $import_data = [
                'finish' => $finish,
                'success' => $success,
                'wrong' => $wrong_email,
                'dupl' => $dupl_emails,
                'total' => $filesize,
                'is_finish' => false,
                'progress' => $progress,
                'file_path' => $file_path,
                'redirect' => '',
                'show_progress_bar' => false,
            ];

            if ($finish == $filesize) {
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                $import_data['is_finish'] = true;
                unset($_SESSION['import_data']);
            } else {
                $_SESSION['import_data'] = $import_data;
            }

            exit(json_encode($import_data));
        }
        
        require_once (ROOT . '/template/admin/views/users/import.php');
    }
    
    
    
    // ЭКСПОРТ ЮЗЕРОВ
    public function actionExport()
    {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_POST['export']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['export_users'])) {
                header("Location: /admin/users?fail");
                exit();
            }

            if ($_POST['type'] == 'all') { // экспорт всех пользователей
                $users = User::getAllUsers();
            } elseif($_POST['type'] == 'custom' && !empty($_POST['user_groups'])) { // экспорт по группам
                $user_groups = implode(",", $_POST['user_groups']);
                $users = User::getAllUsers($user_groups);
            } elseif($_POST['type'] == 'without') {
                $users = User::getAllUsers(false);
            } else {
                $users = null;
            }
            
            $sep = htmlentities(trim($_POST['separator']));

            if ($users) {
                $time = time();
                $str = 'id,name,surname,email,phone,city,address,zipcode,role,enter_time,enter_method,reg_date,last_visit,from_id,status,telegram,instagram,gender,channel_id'.PHP_EOL;

                foreach($users as $user){

                    //$row = implode(",", $user);
                    $row = $user['user_id'].$sep.$user['user_name'].$sep.$user['surname'].$sep.$user['email'].$sep.$user['phone'].$sep.$user['city'].$sep.$user['address'].
                    $sep.$user['zipcode'].$sep.$user['role'].$sep.date("d-m-Y H:i:s", $user['enter_time']).$sep.$user['enter_method'].$sep.
                    date("d-m-Y H:i:s", $user['reg_date']).$sep.date("d-m-Y H:i:s",$user['last_visit']).$sep.$user['from_id'].$sep.$user['status'].
                    $sep.$user['nick_telegram'].$sep.$user['nick_instagram'].$sep.$user['sex'].$sep.$user['channel_id'];
                    $str .= $row.';'.PHP_EOL;

                }

                $write = file_put_contents(ROOT.'/tmp/users_'.$time.'.csv', $str);
                if ($write) {
                    $log = ActionLog::writeLog('users', 'export', 'users', 0, $time, $_SESSION['admin_user'], json_encode($_POST));
                    header("Location: ".$setting['script_url'].'/tmp/users_'.$time.'.csv');
                } else {
                    echo 'Ошибка выборки пользователей';
                }
            }
        }
        
        require_once (ROOT . '/template/admin/views/users/export.php');
    }
    
    
    // ИЗМЕНИТЬ ЮЗЕРА
    public function actionEdit($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_users'])) {
            header("Location: /admin");
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
		$user_cerificates = false;
		
		if (isset($_POST['make_partner'])) {
            $act = Aff::AddUserToPartner($id, 0);
		} //else $act = Aff::AuthorAction($id, 0);
        
        
        if (isset($_POST['user_enter']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            unset($_SESSION['user']);
            unset($_SESSION['name']);
            
            $_SESSION['user'] = $_POST['user_id'];
            $_SESSION['name'] = $_POST['user_name'];
            //$auth = User::Auth($_POST['user_id'], $_POST['user_name']);
            header("Location: /lk");
        }
        
        
        // Отправка письма юзеру
        if (isset($_POST['send']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $subject = htmlentities($_POST['subject']);
            $email = htmlentities($_POST['email']);
            $name = null;
            $letter = $_POST['letter'];
            
            $send = Email::SendMessageToBlank($email, $name, $subject, $letter);
            if($send) {
                header("Location: /admin/users/edit/$id?success");
            }
        }
        
        
        // Спец режим партнёрки
        if (isset($_POST['add_spec_aff']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $spec_aff_params = $_POST['specaff_params'];
            $upd = User::AddProductSpecAff($id, $spec_aff_params);
            if ($upd) {
                header("Location: /admin/users/edit/$id?success");
            }
        }
        
        
        // Изменение продукта в спец.режиме
        if (isset($_POST['spec_id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $item_id = intval($_POST['spec_id']);
            
            if (isset($_POST['save_spec'])) {
                $spec_aff_params = $_POST['specaff_params'];
                
                $upd = User::updateSpecUser($item_id, $spec_aff_params);
                if ($upd) {
                    header("Location: /admin/users/edit/$id?success");
                }
            }
            
            if (isset($_POST['del_spec'])) {
                $del = User::deleteSpecAff($item_id);
                if($del) {
                    header("Location: /admin/users/edit/$id?success");
                }
            }
        }
        
        if(isset($_POST['changecurator']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $new_curator = intval($_POST['newcurator']);
            $curator = intval($_POST['curator_id']);
            $section = intval($_POST['section_id']);
            $training = intval($_POST['training_id']);
            $user_id = intval($_POST['user_id']);
            $ChangeOK = Training::setNewCuratorToUser($user_id, $training, $section, $curator, $new_curator);
            if($ChangeOK){
                header("Location: /admin/users/edit/$id?success");
                exit();
            }
        }

        if(isset($_POST['deletecurator']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $new_curator = intval($_POST['newcurator']);
            $curator = intval($_POST['curator_id']);
            $section = intval($_POST['section_id']);
            $training = intval($_POST['training_id']);
            $user_id = intval($_POST['user_id']);
            $ChangeOK = Training::setNewCuratorToUser($user_id, $training, $section, $curator, $new_curator, true);
            if($ChangeOK){
                header("Location: /admin/users/edit/$id?success");
                exit();
            }
        }
        
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_users'])){
                header("Location: /admin/users?fail");
                exit();
            }

            $old_email = User::getUserNameByID($id);
            if ($old_email['email'] != $_POST['email']) {
                if (User::searchUser($_POST['email'])) {
                    header("Location: /admin/users/edit/$id?dublemail");
                    exit();
                }
            }

            $name = htmlentities($_POST['name']);
            $surname = isset($_POST['surname']) ? htmlentities($_POST['surname']) : null;
            $email = htmlentities($_POST['email']);
            $phone = htmlentities($_POST['phone']);
            $city = htmlentities($_POST['city']);
            $zipcode = htmlentities($_POST['zipcode']);
            $address = htmlentities($_POST['address']);
			$login = htmlentities($_POST['login']);
			$role = htmlentities($_POST['role']);
            $level = intval($_POST['level']);
            
            $sex = htmlentities($_POST['sex']);
            $nick_telegram = htmlentities($_POST['nick_telegram']);
            $nick_instagram = htmlentities($_POST['nick_instagram']);

            if (System::CheckExtensension('autopilot', 1)) { // расширение autopilot
                $vk_url = Autopilot::prepareVkUrl($_POST['vk_url']);
                $vk_url = htmlentities($vk_url);
            } else {
                $vk_url = htmlentities($_POST['vk_url']);
            }

            $is_partner = isset($_POST['is_partner']) ? $_POST['is_partner'] : 0;
            
			$partnership = System::CheckExtensension('partnership', 1);
			if ($partnership) {
                if (isset($_POST['custom_comiss'])) {
                    $upd = Aff::updateCustomComiss($id, intval($_POST['custom_comiss']));
                }
                
                if (isset($_POST['is_author'])) {
                    $act = Aff::AuthorAction($id, 1);
                } else {
                    $act = Aff::AuthorAction($id, 0);
                }
            }
			
 
            $act = isset($_POST['is_curator']) ? Course::AddIsCurator($id, 1) : Course::AddIsCurator($id, 0);
			
            $is_subs = isset($_POST['is_subsc']) ? $_POST['is_subsc'] : 0;
            $groups = isset($_POST['groups']['ids']) ? $_POST['groups']['ids'] : false;
            $groups_dates = isset($_POST['groups']['dates']) ? $_POST['groups']['dates'] : false;
            $curators = isset($_POST['curators']) ? $_POST['curators'] : false;

            $note = htmlentities($_POST['note']);
            $status = htmlentities($_POST['status']);
            
            $pass = !empty($_POST['pass']) ? $_POST['pass'] : '';
            $spec_aff = isset($_POST['spec_aff']) ? intval($_POST['spec_aff']) : 0;


            $edit = User::editUser($id, $name, $email, $phone, $city, $zipcode, $address, $note, $status, $pass, $groups,
                $groups_dates, $is_partner, $is_subs, $role, $login, $surname, $sex, $nick_telegram, $nick_instagram, $level,
                $vk_url, $spec_aff, $curators
            );
            
            if ($edit) {
                $log = ActionLog::writeLog('users', 'edit', 'user', $id, time(), $_SESSION['admin_user'], json_encode($_POST));
                header("Location: /admin/users/edit/$id?success");
            }
        }
        
        if (isset($_POST['blacklist']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            
            if(!isset($acl['change_users'])){
                header("Location: /admin");
                exit();
            }
            
            $email = htmlentities($_POST['email']);
            $act = $_POST['act'] == 'add' ? User::addBlackList($email, 1) : $act = User::addBlackList($email, 0); // добавить/удалить из BL;
            
            if ($act) {
                header("Location: ".$setting['script_url']."/admin/users/edit/$id?success");
            }
        }
        
        $blog = System::CheckExtensension('blog', 1);
        if ($blog) {
            $segment_list = Blog::getUserSegments($id);
        }
        
        $responder = System::CheckExtensension('responder', 1);
        $en_courses = System::CheckExtensension('courses', 1);
        $user = User::getUserDataForAdmin($id);
        if ($user['is_partner'] == 1) {
            $partner = Aff::getPartnerReq($user['user_id']);
        }
        
        $orders = Order::getUserOrders($user['email']);
        $uniq_courses = Course::getUniqCourseInUserMap($id); // список ID тех курсов, которые просмотрены юзером
        $user_groups = User::getGroupByUser($id);
        $user_planes = Member::getAllPlanesByUser($id);
        $all_summ = 0;
        $en_training = System::CheckExtensension('training', 1);
        $uniq_trainings = null;
        if($en_training){
            $user_curators = Training::getAllCuratorsToUser($id);
            $uniq_trainings = Training::getTrainingFromUserMap($id);
            $user_cerificates = Training::getCertificates2User($id);
        }    
        
        //$log_letters = Email::getLog($page = 1, $show_items = null, $pagination = false, $user['email'], $start = false, $finish = false, $subject = false, $filter = false);
        
		$log_letters = Email::getLogByUser($user['email']);
		
		$aff_params = User::getProductsForSpecAff($id);


        require_once (ROOT . '/template/admin/views/users/edit.php');
    }
    
    
    
    
    // СОЗДАТЬ ЮЗЕРА
    public function actionCreate()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['change_users'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['create']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_users'])){
                header("Location: /admin");
                exit();
            }
    
            $email = htmlentities(trim(strtolower(mb_substr($_POST['email'], 0, 50))));
            
            if ($add = User::searchUser($email)) {
                $error_msg = 'Пользователь с данным эмейлом уже существует';
            } else {
                $name = htmlentities(mb_substr($_POST['name'], 0, 255));
                $surname = isset($_POST['surname']) ? htmlentities($_POST['surname']) : null;
                $phone = htmlentities(mb_substr($_POST['phone'],0,25));
                $city = htmlentities(mb_substr($_POST['city'],0,50));
                $login = htmlentities($_POST['login']);
    
                $index = htmlspecialchars(mb_substr($_POST['zipcode'],0, 8));
                $address = htmlentities(mb_substr($_POST['address'],0,255));
                $status = intval($_POST['status']);
                $enter_method = htmlentities($_POST['method']);
    
                $role = htmlentities($_POST['role']);
                $is_client = intval($_POST['is_client']);
                $date = time();
                $param = $date.';admin;0;admin';
                $send_login = intval($_POST['send_login']);
    
                if (isset($_POST['groups'])) {
                    $groups = $_POST['groups'];
                }
    
                if (empty($_POST['pass'])){
                    // Создаём пароль клиенту
                    $pass_data = System::createPass(8);
                    $password = $pass_data['pass'];
                    $hash = $pass_data['hash'];
                } else {
                    $password = htmlentities($_POST['pass']);
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                }
                
                $add = User::AddNewClient($name, $email, $phone, $city, $address, $index, $role, $is_client,
                    $date, $enter_method, $param, $status, $hash, $password, $send_login, $setting['register_letter'],
                    0, $login, null, $surname
                );
                
                // Добавление групп для пользователя
                if(isset($_POST['groups'])){
                    $groups = $_POST['groups'];
                    foreach($groups as $group){
                        User::WriteUserGroup($add['user_id'], $group);
                    }
                }
    
                if($add) {
                    $log = ActionLog::writeLog('users', 'add', 'user', 0, time(), $_SESSION['admin_user'], json_encode($_POST));
                    header("Location: ".$setting['script_url']."/admin/users/edit/{$add['user_id']}?success");
                }
            }
        }
        
        require_once (ROOT . '/template/admin/views/users/create.php');
    }
    
    
    public function actionDelete($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_users'])) {
            header("Location: /admin/users");
            exit();
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            try {
                $delmembertrue = Member::delMemberByIDUser($id);
            } catch (Exception $e) {
                echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            }
            $del = User::deleteUser($id);
            if($del&&$delmembertrue){
                $log = ActionLog::writeLog('users', 'delete', 'user', $id, time(), $_SESSION['admin_user'], 0);
                header("Location: ".$setting['script_url']."/admin/users?success");   
            }
        }
    }
    
    
    
    /*   ГРУППЫ   */
    
    
    public function actionGroup()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_users'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        $groups = User::getUserGroups();
        
        require_once (ROOT . '/template/admin/views/users/groups.php');
    }
    
    
    
    public function actionAddgroup()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_users'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_POST['save']) && !empty($_POST['title']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_users'])){
                header("Location: /admin/users");
                exit;
            }

            $title = $_POST['title'];
            $name = !empty($_POST['name']) ? $_POST['name'] : System::Translit($title);
            $desc = $_POST['desc'];
            $del_tg_chats = isset($_POST['del_tg_chats']) ? $_POST['del_tg_chats'] : null;

            $add = User::AddNewUserGroup($name, $title, $desc, $del_tg_chats);
            if ($add) {
                header("Location: /admin/usergroups?success");
                exit;
            }
        }
        
        require_once (ROOT . '/template/admin/views/users/addgroup.php');
    }
    
    
    
    public function actionEditgroup($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_users'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_users'])){
                header("Location: /admin/users");
                exit;
            }

            $name = $_POST['name'];
            $title = $_POST['title'];
            $desc = $_POST['desc'];
            $del_tg_chats = isset($_POST['del_tg_chats']) ? $_POST['del_tg_chats'] : null;
            
            $edit = User::EditUserGroup($id, $name, $title, $desc, $del_tg_chats);
            if($edit) {
                $log = ActionLog::writeLog('users', 'edit', 'group', $id, time(), $_SESSION['admin_user'], json_encode($_POST));
                header("Location: /admin/usergroups/edit/$id?success");
                exit;
            }
        }
        
        $group = User::getUserGroupData($id);
        
        require_once (ROOT . '/template/admin/views/users/editgroup.php');
    }
    
    
    
    public function actionDelgroup($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_users'])) {
            header("Location: /admin/users");
            exit();
        }
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $id = intval($id);
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = User::deleteGroup($id);
            
            if($del){
                $log = ActionLog::writeLog('users', 'delete', 'group', $id, time(), $_SESSION['admin_user'], 0);
                header("Location: ".$setting['script_url']."/admin/usergroups?success");   
            }
        }
    }
    
    public function actionDelCompleteLesson($lesson_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['del_users'])) {
            header("Location: /admin/users");
            exit();
        }
    
        $lesson_id = intval($lesson_id);
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
        
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token'] && $lesson_id && $user_id) {
            if (isset($_GET['newtr'])) {
                $result = Training::delCompleteLessonFull($user_id, $lesson_id);
            } else {
                $result = Course::delCompleteLesson($user_id, $lesson_id);
            }
    
            if ($result) {
                header("Location: /admin/users/edit/$user_id?success");
            }
        }
    }
    
    public function actionResetPass() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_users'])) {
            header("Location: /admin");
        }
    
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
        $user_name = isset($_GET['user_name']) && $_GET['user_name'] ? $_GET['user_name'] : 'уважаемый пользователь';
        $user_email = isset($_GET['user_email']) ? $_GET['user_email'] : null;
        $token = isset($_GET['token']) ? $_GET['token'] : null;
        
        if ($user_id && $user_name && $user_email && $token == $_SESSION['admin_token']) {
            if ($user_id == 15 && $_SESSION['admin_user'] != 15) {
                header("Location: /admin/users/");
            }
            
            $setting = System::getSetting();
            if (!$setting['pass_reset_letter']) {
                header("Location: /admin/users/");
            }
            
            $pass = System::generateStr(8);
            $res = User::ChangePass($user_id, $pass);
            if ($res) {
                Email::SendLogin($user_name, $user_email, $pass, $setting['pass_reset_letter']);
                header("Location: /admin/users/edit/{$user_id}?success");
            }
        }
    }
}