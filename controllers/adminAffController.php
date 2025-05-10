<?php defined('BILLINGMASTER') or die; 


class adminAffController extends AdminBase {
    
    // ВЫПЛАТЫ ПАРТНЁРСКИХ - СПИСОК КОМУ НУЖНО ВЫПЛАТИТЬ
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $params = unserialize(System::getExtensionSetting('partnership'));
		
		if(isset($_POST['add_transaction']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_aff'])){
                header("Location: /admin");
                exit();
            }
            $user_id = intval($_POST['pid']);
            $order_id = intval($_POST['order_id']);
            $summ = intval($_POST['summ']);
            $date = strtotime($_POST['date']);
            $product_id = 1;
            
            $add = Aff::PartnerTransaction($user_id, $order_id, $product_id, $summ, 0, 1);
            if($add) header("Location: /admin/aff?success");
        }
        
        if(isset($_POST['pay'])){
            
            if(!isset($acl['change_aff'])){
                header("Location: /admin");
                exit();
            }
            $id = intval($_POST['partner']);
            $pay = intval($_POST['summ']);
            $order_id = 0;
            $type = 0;
            $summ = 0;
			$product_id = 0;
            
            $add = Aff::PartnerTransaction($id, $order_id, $product_id, $summ, $pay, $type);
            
            // Отправить письмо о выплате
            if($add){
                Aff::SendPartnerNotifOfPay($id, $pay);
                header("Location: ".$setting['script_url']."/admin/aff?success");
            }
            
        }
        $partners = Aff::getPartnersToPay();
        
        require_once (ROOT . '/template/admin/views/aff/index.php');
    }
    
    
    // СПИСОК ВЫПЛАТ ПАРТНЁРАМ
    public function actionPaystat()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $params = unserialize(System::getExtensionSetting('partnership'));
        
        $partners = Aff::getPartnersToPay(1);
        
        require_once (ROOT . '/template/admin/views/aff/pay_stat.php');
    }
    
    
    // СТАТИСТИКА ВЫПЛАТ АВТОРАМ 
    public function actionAuthorpaystat()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $params = unserialize(System::getExtensionSetting('partnership'));
        
        $authors = Aff::getAuthorsToPay(1);
        
        require_once (ROOT . '/template/admin/views/aff/authors_stat.php');
        
    }
    
    // ВЫПЛАТЫ АВТОРСКИХ - СПИСОК КОМУ НУЖНО ВЫПЛАТИТЬ
    public function actionAuthors()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        if(!isset($acl['change_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $params = unserialize(System::getExtensionSetting('partnership'));  
        
        if(isset($_POST['pay'])){
            
            if(!isset($acl['change_aff'])){
                header("Location: /admin");
                exit();
            }
            
            $id = intval($_POST['partner']);
            $pay = intval($_POST['summ']);
            $order_id = 0;
            $type = 0;
            $summ = 0;
            
            $add = Aff::AuthorTransaction($id, $order_id, 0 ,$summ, $pay, $type);
            
            // Отправить письмо о выплате
            if($add){
                Aff::SendPartnerNotifOfPay($id, $pay);
                header("Location: ".$setting['script_url']."/admin/authors?success");
            }
            
        }
        $authors = Aff::getAuthorsToPay();
        
        require_once (ROOT . '/template/admin/views/aff/authors.php');
    }
    
    
    
    // СТАТИСТИКА НАЧИСЛЕНИЙ ПАРТНЁРАМ
    public function actionUserstat($id)
    {
        $id = intval($id);
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        $params = unserialize(System::getExtensionSetting('partnership')); 
        
        // начисления
        $items = Aff::getHistoryTransaction($id, 1, 'aff');
        
        // Выплаты
        $pays = Aff::getHistoryTransaction($id, 0, 'aff');
        
        $user = User::getUserById($id);
        
        if(isset($_POST['stat_id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_aff'])){
                header("Location: /admin");
                exit();
            }
            $stat_id = intval($_POST['stat_id']);
            $summ = intval($_POST['summ']);
            
            $edit = Aff::reloadComiss($stat_id, $summ, 'aff');
            if($edit)header("Location: /admin/aff/userstat/$id");
            
        }
        
        require_once (ROOT . '/template/admin/views/aff/userstat.php');
        
    }
    
    
    
    // СТАТИСТИКА НАЧИСТЕЛИЙ АВТОРАМ
    public function actionAuthorstat($id)
    {
        $id = intval($id);
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        $params = unserialize(System::getExtensionSetting('partnership'));
		
        // начисления
        $items = Aff::getHistoryTransaction($id, 1, 'author');
        
        // Выплаты
        $pays = Aff::getHistoryTransaction($id, 0, 'author');
        
        $user = User::getUserById($id);
        
        if(isset($_POST['stat_id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_aff'])){
                header("Location: /admin");
                exit();
            }
            $stat_id = intval($_POST['stat_id']);
            $summ = intval($_POST['summ']);
            
            $edit = Aff::reloadComiss($stat_id, $summ, 'author');
            if($edit)header("Location: /admin/authors/userstat/$id");
            
        }
        
        require_once (ROOT . '/template/admin/views/aff/authorstat.php');
        
    }
    
    
    
    public static function actionTop()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        $users = Aff::getTopPartners();
        
        require_once (ROOT . '/template/admin/views/aff/usertop.php');
    }
    
    
    // НАСТРОЙКИ ПАРТНЁРКИ
    public function actionPartnership()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_aff'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['saveaff']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_aff'])){
                header("Location: /admin");
                exit();
            }
            $params = serialize($_POST['aff']);
            $status = intval($_POST['status']);
            
            $save = Aff::SaveAffSetting($params, $status);
        }
        
        $params = unserialize(System::getExtensionSetting('partnership'));
        $enable = System::getExtensionStatus('partnership');
        require_once (ROOT . '/template/admin/views/settings/aff.php');
    }
    
    
}