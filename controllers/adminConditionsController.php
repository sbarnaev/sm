<?php defined('BILLINGMASTER') or die; 


class adminConditionsController extends AdminBase {
    
    
    
    // СПИСОК УСЛОВИЙ
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_conditions'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $conditions_list = Conditions::getConditionsList();
        
        require_once (ROOT . '/template/admin/views/conditions/index.php');
    }
    
    
    
    // ДОБАВИТЬ УСЛОВИЕ
    public function actionAdd()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_conditions'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_POST['add']) && !empty($_POST['type']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            
            if (!isset($acl['change_conditions'])) {
                header("Location: /admin");
                exit();
            }
            $create_date = time();
            $name = htmlentities($_POST['name']);
            $type = intval($_POST['type']);
            $value_xx = intval($_POST['value_xx']);
            $status = intval($_POST['status']);
            $desc = htmlentities($_POST['desc']);
            
            $use_cron = intval($_POST['use_cron']);
            $period = intval($_POST['period']);
            $sql = htmlentities($_POST['sql']);
            
            $add_groups = isset($_POST['add_groups']) ? base64_encode(serialize($_POST['add_groups'])) : null;
            $del_groups = isset($_POST['del_groups']) ? base64_encode(serialize($_POST['del_groups'])) : null;
    
            $delivery_id = intval($_POST['delivery']);
            $delivery_unsub = isset($_POST['delivery_unsub']) ? base64_encode(serialize($_POST['delivery_unsub'])) : null;
            
            $send_letter = intval($_POST['send_letter']);
            $subject = htmlentities($_POST['subject']);
            $letter = $_POST['letter'];
    
            $send_sms = intval($_POST['send_sms']);
            $message = htmlentities($_POST['message']);
            
            $add = Conditions::addNewCondition($name, $type, $value_xx, $status, $desc, $use_cron, $period, $sql,
                $add_groups, $del_groups, $delivery_id, $delivery_unsub, $send_letter, $subject, $letter, $send_sms,
                $message, $create_date
            );
            
            if ($use_cron == 0) {
                //выполнить сразу
                $cond = Conditions::getCondByCreateDate($create_date);
                if ($cond) {
                    $act = Conditions::renderCond($cond, $create_date);
                }
            }
            
            if ($add) {
                header("Location: /admin/conditions/?success");
            }
        }
        
        
        require_once (ROOT . '/template/admin/views/conditions/add.php');
    }
    
    
    // ИЗМЕНИТЬ УСЛОВИЕ
    public function actionEdit($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['change_conditions'])) {
            header("Location: /admin");
            exit();
        }
        
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_POST['edit']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            
            $name = htmlentities($_POST['name']);
            $type = intval($_POST['type']);
            $value_xx = intval($_POST['value_xx']);
            $status = intval($_POST['status']);
            $desc = htmlentities($_POST['desc']);
            
            $use_cron = intval($_POST['use_cron']);
            $period = intval($_POST['period']);
            $sql = htmlentities($_POST['sql']);
            
            $add_groups = isset($_POST['add_groups']) ? base64_encode(serialize($_POST['add_groups'])) : null;
            $del_groups = isset($_POST['del_groups']) ? base64_encode(serialize($_POST['del_groups'])) : null;
    
            $delivery_unsub = isset($_POST['delivery_unsub']) ? base64_encode(serialize($_POST['delivery_unsub'])) : null;
            $delivery_id = intval($_POST['delivery']);
            
            $send_letter = intval($_POST['send_letter']);
            $subject = htmlentities($_POST['subject']);
            $letter = $_POST['letter'];
    
            $send_sms = intval($_POST['send_sms']);
            $message = htmlentities($_POST['message']);
            
            $edit = Conditions::editCondition($id, $name, $type, $value_xx, $status, $desc, $use_cron, $period, $sql,
                $add_groups, $del_groups, $delivery_id, $delivery_unsub, $send_letter, $subject, $letter, $send_sms, $message
            );
            
            if ($use_cron == 0 && $status == 1) {
                //выполнить сразу
                $cond = Conditions::getConditionData($id);
                if ($cond) {
                    $act = Conditions::renderCond($cond, time());
                }
            }
            
            if ($edit) {
                header("Location: /admin/conditions/edit/$id?success");
            }
            
        }
        
        $condition = Conditions::getConditionData($id);
        
        require_once (ROOT . '/template/admin/views/conditions/edit.php');
    }
    
    
    
    // УДАЛИТЬ УСЛОВИЕ
    public function actionDel($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_conditions'])) {
            header("Location: /admin");
        }
        
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $id = intval($id);
        
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Conditions::delCondition($id);
            if ($del) {
                header("Location: /admin/conditions/?success");
            }
        }
    }
}