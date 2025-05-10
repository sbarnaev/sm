<?php defined('BILLINGMASTER') or die;

class adminRedirectController extends AdminBase {
    
    
    
    public static function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_rdr'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $cat_id = null;
        $url = null;

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $total = Redirect::totalRedirect();
        $is_pagination = true;
        $pagination = new Pagination($total, $page, $setting['show_items']);
        
        if(isset($_POST['filter'])){
            $cat_id = intval($_POST['cat_id']);
            $url = $_POST['url'];
            $is_pagination = false;
        }
        
        $redirect_list = Redirect::getRedirectList($page, $setting['show_items'], $cat_id, $url);
        require_once (ROOT . '/template/admin/views/redirect/index.php');
        
    }
    
    
    
    
    public function actionAdd()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_rdr'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $now = time();
        
        if(isset($_POST['addred']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_rdr'])){
                header("Location: /admin");
                exit();
            }
            $title = htmlentities($_POST['title']);
            $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
            $rdr_desc = htmlentities($_POST['rdr_desc']);
            $url = $_POST['url'];
            $alt_url = $_POST['alt_url'];
            $status = intval($_POST['status']);
            
            if(isset($_POST['limit'])) $limit = intval($_POST['limit']);
            else $limit = 0;
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            else $end = $now + 330720000; 
            $hits = 0;
            
            $add = Redirect::addRedirect($title, $cat_id, $rdr_desc, $url, $alt_url, $status, $limit, $end, $hits, $now);
            if($add) {
                $setting = System::getSetting();
                header("Location: ".$setting['script_url']."/admin/redirect");
                exit();
            }
            
        }
        
        require_once (ROOT . '/template/admin/views/redirect/add.php');
    }
    
    
    
    public function actionEdit($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_rdr'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getMainSetting();
        
        if(isset($_POST['edit']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_rdr'])){
                header("Location: /admin");
                exit();
            }
            $now = time();
            $title = htmlentities($_POST['title']);
            $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
            $rdr_desc = htmlentities($_POST['rdr_desc']);
            $url = $_POST['url'];
            $alt_url = $_POST['alt_url'];
            $status = intval($_POST['status']);
            
            if(isset($_POST['limit'])) $limit = intval($_POST['limit']);
            else $limit = 0;
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            else $end = $now + 330720000; 
            
            $edit = Redirect::editRedirect($id, $title, $cat_id, $rdr_desc, $url, $alt_url, $status, $limit, $end);
            if($edit) header("Location: ".$setting['script_url']."/admin/redirect/edit/$id?success");
            
        }
        
        $redirect = Redirect::getRedirectData($id);
        
        require_once (ROOT . '/template/admin/views/redirect/edit.php');
    }
    
    
    
    
    
    
    // СПИСОК КАТЕГОРИЙ
    public function actionCats()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_rdr'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        
        $cat_list = Redirect::getRdrCatList();
        
        require_once (ROOT . '/template/admin/views/redirect/cats.php');
    }
    
    
    // СОЗДАТЬ КАТЕГОРИЮ
    public function actionAddcat()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_rdr'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        
        if(isset($_POST['addcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_rdr'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $desс = htmlentities($_POST['cat_desc']);
            
            $add = Redirect::addCat($name, $desс);
            if($add) header("Location: ".$setting['script_url']."/admin/redirect/cats");
            
        }
        
        require_once (ROOT . '/template/admin/views/redirect/addcat.php');
    }
    
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public function actionEditcat($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_rdr'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if(isset($_POST['editcat'])&& isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_rdr'])){
                header("Location: /admin");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $desс = htmlentities($_POST['cat_desc']);
            
            $edit = Redirect::editCat($id, $name, $desс);
            if($edit) header("Location: ".$setting['script_url']."/admin/redirect/editcat/$id?success");
            
        }
        
        $cat = Redirect::getCat($id);
        
        require_once (ROOT . '/template/admin/views/redirect/editcat.php');
    }
    
    
    
    // УДАЛИТЬ РЕДИРЕКТ
    public function actionDel($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_rdr'])) {
            header("Location: /admin");
            exit();   
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Redirect::delRedirect($id);
            if($del) header("Location: ".$setting['script_url']."/admin/redirect");
        }
    }
    
    
    
    // УДАЛИТЬ КАТЕГОРИЮ
    public function actionDelcat($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_rdr'])) {
            header("Location: /admin");
            exit();   
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Redirect::delCat($id);
            if($del) header("Location: ".$setting['script_url']."/admin/redirect/cats?success");
            else header("Location: ".$setting['script_url']."/admin/redirect/cats?fail");
        }
    }
    
    
}