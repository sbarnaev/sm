<?php defined('BILLINGMASTER') or die; 


class adminOrganizationController extends AdminBase {
    
    
    // СПИСОК ОРГАНИЗАЦИЙ
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_main_tunes'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();
        
        $org_list = Organization::getOrgList();
        
        require_once (ROOT . '/template/admin/views/organization/index.php');
    }
    
    
    
    // ДОБАВИТЬ ОРГАНИЗАЦИЮ
    public function actionAdd()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_main_tunes'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();
        
         if(isset($_POST['add_org']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            $org_name = htmlentities($_POST['name']);
            $org_desc = htmlentities($_POST['org_desc']);
            $requisits = json_encode($_POST['requisits']);
            $oferta = $_POST['oferta'];
            $payments = json_encode($_POST['payments']);
            $status = intval($_POST['status']);
            
            $add = Organization::addOrganization($org_name, $org_desc, $requisits, $oferta, $payments, $status);
            if($add) header("Location: /admin/organizations?success");
                        
         }
        
        require_once (ROOT . '/template/admin/views/organization/add.php');
    }
    
    
    // ИЗМЕНИТЬ ОРГАНИЗАЦИЮ
    public function actionEdit($org_id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_main_tunes'])) {
            header("Location: /admin");
        }
        $org_id = intval($org_id);
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        $org = Organization::getOrgData($org_id);
        $req = json_decode($org['requisits'], true);
        $payments = json_decode($org['payments'], true);
        
        if(isset($_POST['edit_org']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            $org_name = htmlentities($_POST['name']);
            $org_desc = htmlentities($_POST['org_desc']);
            $requisits = json_encode($_POST['requisits']);
            $oferta = $_POST['oferta'];
            $payments = json_encode($_POST['payments']);
            $status = intval($_POST['status']);
            
            $edit = Organization::editOrganization($org_id, $org_name, $org_desc, $requisits, $oferta, $payments, $status);
            if($edit) header("Location: /admin/organizations/edit/$org_id?success");
            
        }
        
        require_once (ROOT . '/template/admin/views/organization/edit.php');
    }
    
}