<?php defined('BILLINGMASTER') or die; 


class adminBlogController extends AdminBase {
    
    //СПИСОК МАТЕРИАЛОВ
    public function actionIndex()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        
        $name = $_SESSION['admin_name'];
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        $setting = System::getSetting();
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        /**
         *  ПАГИНАЦИЯ 
         */
        
		if(isset($_POST['filter'])){
			if(!empty($_POST['title']) || $_POST['cat_id'] != 0 || $_POST['status'] != 2){
				$title = $_POST['title'];
				$cat_id = intval($_POST['cat_id']);
				$status = intval($_POST['status']);
				$is_pagination = false;
				$post_list = Blog::getPostFilterList($title, $cat_id, $status);
			} else header("Location: /admin/blog");
			
		} else {

            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
			$total_post = Blog::countAllPost(); // кол-во записей всего.
			$is_pagination = true;
			$pagination = new Pagination($total_post, $page, $setting['show_items']);
			
			$post_list = Blog::getPostList($page, $setting['show_items']);
		}
        
        require_once (ROOT . '/template/admin/views/blog/index.php');
    }
    
    
    // СОЗДАТЬ ЗАПИСЬ
    public function actionAddpost()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        $rubric_list = Blog::getRubricList();
		$setting = System::getSetting();
        $now = time();
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        if(isset($_POST['addpost']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            $name = htmlentities(trim($_POST['name']));
            $rubric_id = intval($_POST['rub_id']);
            $img_alt = htmlentities($_POST['img_alt']);
			$show_cover = intval($_POST['show_cover']);
            $short_desc = htmlentities($_POST['short_desc']);
            $status = intval($_POST['status']);
            $text = $_POST['text'];
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];
            
            if($_POST['author_id'] != null) $author_id = intval($_POST['author_id']);
            else $author_id = null;
            
            if(empty($_POST['title'])) $title = $name;
            else $title = htmlentities(trim($_POST['title'])); 
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            if(!empty($_POST['start'])) $start = strtotime($_POST['start']);
            else $start = $now;
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            else $end = $now + 330720000;
            
			$img = null;
			
            if(isset($_FILES['cover'])){
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке 
                
                $folder = ROOT . '/images/post/cover/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            }
            
            $add = Blog::addPost($name, $rubric_id, $img, $img_alt, $short_desc, $status, $text, $alias, $title, $meta_desc, $meta_keys, 
            $start, $end, $now, $show_cover, $author_id);
            
            if($add) {
                $log = ActionLog::writeLog('blog', 'add', 'post', 0, $now, $_SESSION['admin_user'], json_encode($_POST));
                header("Location: ".$setting['script_url']."/admin/blog?success");   
            }
            
        }
        
        require_once (ROOT . '/template/admin/views/blog/add_post.php');
    }
    
    
    
    // РЕДАКТИРОВАТЬ ЗАПИСЬ
    public function actionEditpost($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        $now = time();
        
        $params = unserialize(System::getExtensionSetting('blog')); 
        
        if(isset($_POST['editpost']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            
            $name = htmlentities(trim($_POST['name']));
            $rubric_id = intval($_POST['rub_id']);
            $img_alt = htmlentities($_POST['img_alt']);
			$show_cover = intval($_POST['show_cover']);
            $short_desc = htmlentities($_POST['short_desc']);
            $status = intval($_POST['status']);
            $text = $_POST['text'];
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];
            
            if($_POST['author_id'] != null) $author_id = intval($_POST['author_id']);
            else $author_id = null;
            
            if(empty($_POST['title'])) $title = $name;
            else $title = trim(htmlentities($_POST['title'])); 
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            if(!empty($_POST['start'])) $start = strtotime($_POST['start']);
            else $start = $now;
            if(!empty($_POST['end'])) $end = strtotime($_POST['end']);
            else $end = $now + 330720000;
			
			$sort = intval($_POST['sort']);
            
            $custom_code = $_POST['custom_code'];
            
            if(isset($_FILES["cover"]["tmp_name"]) && $_FILES["cover"]["size"] != 0){
                
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке 
                
                $folder = ROOT . '/images/post/cover/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if(is_uploaded_file($tmp_name)){
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            } else {
                if(isset($_POST['current_img'])) $img = $_POST['current_img'];
				else $img = null;
            }
            
            $edit = Blog::editPost($id, $name, $rubric_id, $img, $img_alt, $short_desc, $status, $text, $alias, $title, $meta_desc, $meta_keys, 
            $start, $end, $show_cover, $custom_code, $author_id, $sort);
            
            if($edit){
                $log = ActionLog::writeLog('blog', 'edit', 'post', $id, $now, $_SESSION['admin_user'], json_encode($_POST));
                header("Location: ".$setting['script_url']."/admin/blog/edit/$id?success");
            } 
        }
        
        $rubric_list = Blog::getRubricList();
        $post = Blog::getPostDataByID($id);
        
        require_once (ROOT . '/template/admin/views/blog/edit_post.php');
    }
    
    
    
    // УДАЛИТЬ ЗАПИСЬ
    public function actionDelete($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_blog'])) {
            header("Location:/admin/blog");
            exit();
        }
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Blog::delPost($id);
            if($del){
                $log = ActionLog::writeLog('blog', 'delete', 'post', $id, time(), $_SESSION['admin_user'], null);
                header("Location: ".$setting['script_url']."/admin/blog?success");  
            } 
            else header("Location: ".$setting['script_url']."/admin/blog?fail");
        }
    }
    
    /**
     *   РУБРИКИ 
     */
    
    
    // СПИСОК РУБРИК
    public function actionRubrics()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        $rubric_list = Blog::getRubricList();
        
        require_once (ROOT . '/template/admin/views/blog/rubrics.php');
    }
    
    
    
    public function actionAddrubric()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog')); 
        
        if(isset($_POST['add']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $short_desc = $_POST['short_desc'];
            $status = intval($_POST['status']);
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];
            
            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title']; 
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            
            $add = Blog::addRubric($name, $alias, $title, $short_desc, $status, $meta_desc, $meta_keys);
            
            if($add){
                $log = ActionLog::writeLog('blog', 'add', 'category', 0, time(), $_SESSION['admin_user'], json_encode($_POST));
                header("Location: ".$setting['script_url']."/admin/rubrics?success");   
            }
            
        }
        
        require_once (ROOT . '/template/admin/views/blog/add_rubric.php');
    }
    
    
    
    public function actionEditrubric($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        $rubric = Blog::getRubricDataByID($id);
        
        if(isset($_POST['edit']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $short_desc = $_POST['short_desc'];
            $status = intval($_POST['status']);
            if(empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];
            
            if(empty($_POST['title'])) $title = $name;
            else $title = $_POST['title']; 
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
			
			$access_type = intval($_POST['access_type']);
            $groups = !empty($_POST['groups']) ? json_encode($_POST['groups']) : null;
            $planes = !empty($_POST['planes']) ? json_encode($_POST['planes']) : null;
            
            $edit = Blog::EditRubric($id, $name, $alias, $title, $short_desc, $status, $meta_desc, $meta_keys, $access_type, $groups, $planes);
            if($edit){
                $log = ActionLog::writeLog('blog', 'edit', 'category', $id, time(), $_SESSION['admin_user'], json_encode($_POST));
                header("Location: ".$setting['script_url']."/admin/rubrics/edit/$id?success");
            }
            
        }
        
        require_once (ROOT . '/template/admin/views/blog/edit_rubric.php');
    }
    
    
    public function actionDelrubric($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_blog'])) {
            header("Location:/admin/blog");
            exit();
        }
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Blog::deleteRubric($id);
            if($del){
                $log = ActionLog::writeLog('blog', 'delete', 'category', $id, time(), $_SESSION['admin_user'], 0);
                header("Location: ".$setting['script_url']."/admin/rubrics?success");
            }
            else header("Location: ".$setting['script_url']."/admin/rubrics?fail");
        }
    }
    
    
    // НАСТРОЙКИ БЛОГА
    public function actionSettings()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if(isset($_POST['saveblog']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin");
                exit();
            }
            
            $params = serialize($_POST['blog']);
            $status = intval($_POST['status']);
            
            $save = System::SaveExtensionSetting('blog', $params, $status);
        }
        
        $params = unserialize(System::getExtensionSetting('blog'));
        $enable = System::getExtensionStatus('blog');
        require_once (ROOT . '/template/admin/views/blog/setting.php');
    }
    
    
    /**
     *   ИНТЕРЕСЫ / СЕГМЕНТЫ
     */
    
    // СПИСОК СЕГМЕНТОВ
    public function actionSegments()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        $list = Blog::getSegmentsList();
        
        require_once (ROOT . '/template/admin/views/blog/segments.php');
    }
    
    
    
    // СОЗДАТЬ СЕГМЕНТ
    public function actionAddsegment()
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        
        if(isset($_POST['add']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $desc = htmlentities($_POST['desc']);
            
            $add = Blog::addSegment($name, $desc);
            if($add) header("Location: ".$setting['script_url']."/admin/segments?success");
            
        }
        
        require_once (ROOT . '/template/admin/views/blog/add_segment.php');
    }
    
    
    // РЕДАКТИРОВАТЬ СЕГМЕНТ 
    public function actionEditsegment($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['show_blog'])) header("Location:/admin");
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        $params = unserialize(System::getExtensionSetting('blog'));
        
        // Редактирование сегмента
        if(isset($_POST['edit']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            $name = htmlentities($_POST['name']);
            $desc = htmlentities($_POST['desc']);
            
            $edit = Blog::editSegment($id, $name, $desc);
            if($edit) header("Location: ".$setting['script_url']."/admin/segments/edit/$id");
        }
        
        
        // Добавление URL 
        if(isset($_POST['add_url']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']){
            
            if(!isset($acl['change_blog'])) {
                header("Location:/admin/blog");
                exit();
            }
            $url = trim(htmlentities($_POST['url']));
            $url = parse_url($url);
            $url = $url['path'];
            $url_desc = htmlentities($_POST['url_desc']);
            
            $add_url = Blog::AddURLSegment($id, $url, $url_desc);
            if($add_url) header("Location: ".$setting['script_url']."/admin/segments/edit/$id?success");
            
        }
        
        $segment = Blog::getSegmentData($id);
        $url_list = Blog::getUrllist($id);
        
        require_once (ROOT . '/template/admin/views/blog/edit_segment.php');
    }
    
    
    
    // УДАЛИТЬ СЕГМЕНТ
    public function actionDelsegment($id)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_blog'])) {
            header("Location:/admin/blog");
            exit();
        }

        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Blog::delSegment($id);
            if($del) header("Location: ".$setting['script_url']."/admin/segments");
        }
    }
    
    
    // УДАЛЕНИЕ URL 
    public function actionDelurl($id, $sid)
    {
        $acl = self::checkAdmin();
        if(!isset($acl['del_blog'])) {
            header("Location:/admin/blog");
            exit();
        }
        $name = $_SESSION['admin_name'];
		$setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if(!$blog) exit('Blog not installed');
        
        if(isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']){
            $del = Blog::delURLSegment($id);
            if($del) header("Location: ".$setting['script_url']."/admin/segments/edit/$sid");
        }
    }
    
    
}