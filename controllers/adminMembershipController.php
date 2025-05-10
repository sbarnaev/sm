<?php defined('BILLINGMASTER') or die; 


class adminMembershipController extends AdminBase {
    
    
    // Страница планов подписки
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        
        $params = unserialize(Member::getMembershipSetting());
        
        // Список планов подписки
        $planes = Member::getPlanes();
        
        require_once (ROOT . '/template/admin/views/membership/index.php');
    }


    /**
     * Лог продлений подписок
     */
    public function actionLog()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();

        $filter = [
            'subs_map_id' => isset($_GET['subs_map_id']) && $_GET['subs_map_id'] ? htmlentities($_GET['subs_map_id']) : null,
            'plane_id' => isset($_GET['plane_id']) && $_GET['plane_id'] ? intval($_GET['plane_id']) : null,
            'user_id' => isset($_GET['user_id']) && $_GET['user_id'] ? intval($_GET['user_id']) : null,
            'start_date' =>  isset($_GET['start_date']) && $_GET['start_date'] ? strtotime($_GET['start_date']) : null,
            'finish_date' => isset($_GET['finish_date']) && $_GET['finish_date'] ? strtotime($_GET['finish_date']) : null,
        ];
        $filter['is_filter'] = array_filter($filter, 'strlen') ? true : false;

        $total_items = Member::getTotalMemberLog($filter);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pagination = new Pagination($total_items, $page, $setting['show_items']);

        $params = unserialize(Member::getMembershipSetting());
        $logs = Member::getMemberLog($filter, $page, $setting['show_items']);

        require_once (ROOT . '/template/admin/views/membership/log.php');
    }


    /**
     * СОЗДАТЬ НОВЫЙ ПЛАН ПОДПИСКИ
     */
    public function actionAddsubs()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_member'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $params = unserialize(Member::getMembershipSetting());
		$setting = System::getSetting();
        
        if (isset($_POST['add']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_member'])) {
                System::redirectUrl("/admin");
            }

            $add = Member::AddNewPlane($_POST);
            if ($add) {
                $log = ActionLog::writeLog('membership', 'add', 'plane', 0, time(),
                    $_SESSION['admin_user'], json_encode($_POST)
                );
                System::redirectUrl('/admin/membersubs', true);
            }
            
        }
        
        require_once (ROOT . '/template/admin/views/membership/add_plane.php');
    }


    /**
     * ИЗМЕНИТЬ ПЛАН ПОДПИСКИ
     * @param $id
     */
    public function actionEditsubs($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) {
            header("Location: /admin");
        }
    
        $id = intval($id);
        $name = $_SESSION['admin_name'];
        $params = unserialize(Member::getMembershipSetting());
		$setting = System::getSetting();
        
        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if(!isset($acl['change_member'])){
                System::redirectUrl('/admin');
            }

            $edit = Member::editPlane($id, $_POST);
            if ($edit) {
                $log = ActionLog::writeLog('membership', 'edit', 'plane', $id, time(), $_SESSION['admin_user'], json_encode($_POST));
                System::redirectUrl("/admin/membersubs/edit/$id", $edit);   
            }
        }
        
        // Данные плана подписки
        $plane = Member::getPlaneByID($id);
        $selected = unserialize(base64_decode($plane['select_payments']));
        $related_plane_arr = $plane['related_planes'] ? explode(",", $plane['related_planes']) : null;
        
        require_once (ROOT . '/template/admin/views/membership/edit_plane.php');
    }
    
    
    
    // Страница уровней доступа
    public function actionLevels()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $params = unserialize(Member::getMembershipSetting());
        
        $levels = Member::getLevelsList();
        
        require_once (ROOT . '/template/admin/views/membership/levels.php');
    }
    
    
    
    // СОЗДАТЬ НОВЫЙ УРОВЕНЬ
    public function actionAddlevel()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $params = unserialize(Member::getMembershipSetting());
        
        
        if(isset($_POST['add']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_member'])){
                header("Location: /admin");
                exit();
            }
            $add = Member::AddNewLevel(htmlentities($_POST['name']), htmlentities($_POST['desc']));
            if($add) header("Location: ".$setting['script_url']."/admin/memberlevels?success");
            
        }
        
        require_once (ROOT . '/template/admin/views/membership/add_level.php');
        
    }
    

    
    
    // УДАИТЬ ПЛАН ПОДПИСКИ
    public function actionDelsubs($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_member'])) {
            header("Location: /admin/membersubs");
            exit();   
        }
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $id = intval($id);
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Member::DeletePlane($id);
            if($del){
                $log = ActionLog::writeLog('membership', 'delete', 'plane', $id, time(), $_SESSION['admin_user'], 0);
                header("Location: ".$setting['script_url']."/admin/membersubs?success");   
            } else header("Location: ".$setting['script_url']."/admin/membersubs?fail");
        }
    }


    /**
     * Страница купленных подписок
     */
    public function actionUsers()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_member'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $params = unserialize(Member::getMembershipSetting());

        if (isset($_GET['reset'])) {
            unset($_SESSION['filter_memberusers']);
        }

        $filter = !isset($_POST['filter']) && isset($_SESSION['filter_memberusers']) ? $_SESSION['filter_memberusers'] : [
            'plane' => isset($_POST['filter']) && $_POST['plane'] ? htmlentities($_POST['plane']) : null,
            'email' => isset($_POST['filter']) && $_POST['email'] ? htmlentities($_POST['email']) : null,
            'name' => isset($_POST['filter']) && $_POST['name'] ? htmlentities($_POST['name']) : null,
            'surname' => isset($_POST['filter']) && $_POST['surname'] ? htmlentities($_POST['surname']) : null,
            'status' => isset($_POST['filter']) && $_POST['status'] != '' ? intval($_POST['status']) : null,
            'pay_status' => isset($_POST['filter']) && $_POST['pay_status'] != '' ? intval($_POST['pay_status']) : null,
            'start' => isset($_POST['filter']) && $_POST['start'] ? htmlentities($_POST['start']) : null,
            'start_from' => isset($_POST['filter']) && $_POST['start_from'] ? strtotime($_POST['start_from']) : null,
            'start_to' => isset($_POST['filter']) && $_POST['start_to'] ? strtotime($_POST['start_to']) : null,
            'finish' => isset($_POST['filter']) && $_POST['finish'] ? htmlentities($_POST['finish']) : null,
            'finish_from' => isset($_POST['filter']) && $_POST['finish_from'] ? strtotime($_POST['finish_from']) : null,
            'finish_to' => isset($_POST['filter']) && $_POST['finish_to'] ? strtotime($_POST['finish_to']) : null,
            'canceled' => isset($_POST['filter']) && $_POST['canceled'] ? htmlentities($_POST['canceled']) : null,
            'canceled_from' => isset($_POST['filter']) && $_POST['canceled_from'] ? strtotime($_POST['canceled_from']) : null,
            'canceled_to' => isset($_POST['filter']) && $_POST['canceled_to'] ? strtotime($_POST['canceled_to']) : null,
        ];

        if (isset($_POST['load_csv']) && isset($_SESSION['filter_memberusers'])) {
            $filter['is_filter'] = true;
        } else {
            $filter['is_filter'] = array_filter($filter, 'strlen') ? true : false;
            if ($filter['is_filter']) {
                $_SESSION['filter_memberusers'] = $filter;
            }
        }
        

        $time = time();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $total_items = Member::getTotalPlanesWithFilter($filter);
        $is_pagination = !isset($_POST['load_csv']) ? true : false;
        $pagination = new Pagination($total_items, $page, $setting['show_items']);

        $members = Member::getMemberListWithFilter($filter, $page, $setting['show_items'], $is_pagination);

        if (isset($_POST['load_csv']) && $members) {
            $fields = [
                'id', 'subs_id', 'subscription_id',
                'user_id', 'user_name', 'email', 'login',
                'status', 'create_date', 'begin', 'end'
            ];
            $count_fields = count($fields);
            $csv = implode(';', $fields) . PHP_EOL;

            foreach ($members as $key => $member) {
                foreach ($fields as $_key => $field) {
                    $value = $member[$field] && in_array($field, ['create_date', 'begin', 'end']) ? date("d.m.Y H:i:s", $member[$field]) : $member[$field];
                    $csv .= $value . ($_key < $count_fields - 1 ? ';' : '');
                }
                $csv .= PHP_EOL;
            }

            $write = file_put_contents(ROOT . "/tmp/memberusers_$time.csv", $csv);
            if ($write) {
                System::redirectUrl("/tmp/memberusers_$time.csv");
            }
        }

        require_once (ROOT . '/template/admin/views/membership/members.php');
    }


    /**
     * ИЗМЕНИТЬ ПОДПИСКУ ЮЗЕРА
     * @param $id
     */
    public function actionEdituser($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_member'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $params = unserialize(Member::getMembershipSetting());

        $member = Member::getMemberRow($id);

        if (!$member) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }

        $planes = Member::getPlanes();
        $time = time();

        if (isset($_POST['edit']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $subscription_id = htmlentities($_POST['subscription_id']);
            $end = strtotime($_POST['end']);
            $plane_id = isset($_POST['plane_id']) ? intval($_POST['plane_id']) : 0;
            
            $status = intval($_POST['status']);
			$lc_id = isset($_POST['lc_id']) ? intval($_POST['lc_id']) : 0;
            $recurrent_cancelled = isset($_POST['recurrent_cancelled']) ? intval($_POST['recurrent_cancelled']) : null;

            $edit = Member::editUserSubscript($id, $subscription_id, $end, $plane_id, $status, $recurrent_cancelled, $lc_id);
            if ($edit) {
                $log = ActionLog::writeLog('membership', 'edit', 'member', $id, $time, $_SESSION['admin_user'], json_encode($_POST));
                header("Location: /admin/memberusers/edit/$id?success");
            }
        }
        
        require_once (ROOT . '/template/admin/views/membership/edit_member.php');
        
    }
    
    
    // ДОБАВИТЬ УЧАСТНИКА
    public static function actionAddmember()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $params = unserialize(Member::getMembershipSetting());
        
        
        if(isset($_POST['add']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_member'])){
                header("Location: /admin");
                exit();
            }
            
            $add = Member::addMember(intval($_POST['plane']), intval($_POST['user_id']));
            if($add){
                $log = ActionLog::writeLog('membership', 'add', 'member', 0, time(), $_SESSION['admin_user'], json_encode($_POST));
                header("Location: ".$setting['script_url']."/admin/memberusers?success");   
            }
            else header("Location: ".$setting['script_url']."/admin/memberusers?fail");
            
        }
        
        require_once (ROOT . '/template/admin/views/membership/add_member.php');
    }
    
    
    // УДАЛИТЬ УЧАСТНИКА
    public function actionDelmember($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_member'])) {
            header("Location: /admin/membersubs");
            exit();   
        }
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $id = intval($id);
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            
            if(isset($_GET['action'])){
                
                if($_GET['action'] == 'delete') $act = Member::delMember($id);
                if($_GET['action'] == 'pause') $act = Member::pauseMember($id, 0);
                if($_GET['action'] == 'play') $act = Member::pauseMember($id, 1);
                
                
                if($act){
                    $log = ActionLog::writeLog('membership', 'delete', 'member', $id, time(), $_SESSION['admin_user'], 0);
                    header("Location: ".$setting['script_url']."/admin/memberusers?success");   
                }
                else header("Location: ".$setting['script_url']."/admin/memberusers?fail");  
            }
        }
    }
    
    
    
    // НАСТРОЙКИ 
    public function actionSettings()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_member'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['savemember']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_member'])){
                header("Location: /admin");
                exit();
            }
            $params = serialize($_POST['member']);
            $status = intval($_POST['status']);
            
            $save = Member::SaveBlogSetting($params, $status);
        }
        
        $params = unserialize(Member::getMembershipSetting());
        $enable = Member::getMemberShipStatus();
        require_once (ROOT . '/template/admin/views/membership/setting.php');
    }
    
    // ЭКСПОРТ УЧАСТНИКОВ
    public function actionExport() {
        $acl = self::checkAdmin();
        $name = $_SESSION['admin_name'];


        if (isset($_POST['export']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['show_member'])) {
                header("Location: /admin");
            }

            $setting = System::getSetting();
            $params = unserialize(Member::getMembershipSetting());

            $members = Member::getMemberList();
            if ($members) {
                $str = 'id,user_name,email,subscription_id,subs_id,create_date,end'.PHP_EOL;
                foreach ($members as $member) {
                    $row = array(
                        'id' => $member['id'],
                        'user_name' => $member['user_name'],
                        'email' => $member['email'],
                        'subscription_id' => $member['subscription_id'],
                        'subs_id' => $member['subs_id'],
                        'create_date' => date("d.m.Y H:i", $member['create_date']),
                        'end' => date("d.m.Y H:i", $member['end']),
                    );

                    $str.= implode(',', array_values($row)).';'.PHP_EOL;
                }

                $csv = '/tmp/users_' . time() . '.csv';
                $write = file_put_contents(ROOT.$csv, $str);
                if ($write) {
                    header("Location: $csv");
                } else {
                    echo 'Ошибка выборки участников';
                }
            }
        }

        require_once (ROOT . '/template/admin/views/membership/export.php');
    }
}