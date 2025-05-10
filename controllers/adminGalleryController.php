<?php defined('BILLINGMASTER') or die; 

class adminGalleryController extends AdminBase {

    const SECRET = 'gallery_rus-rGw@gh*$$aggG438hjr12^';
    
    // ГЛАВНАЯ СТРАНИЦА ГАЛЕРЕИ = список фото
    public function actionIndex($page = 1)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) System::redirectUrl("/admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $params = unserialize(System::getExtensionSetting('gallery'));
        
        $page = intval($page);
        $total = Gallery::totalIMG(); //выбираем общее кол-во элементов
        $pagination = new Pagination($total, $page, $setting['show_items'], 'page-');
        
        if (isset($_POST['filter'])) {
            $cat_id = intval($_POST['cat_id']);
            $is_pagination = false; 
            $img_list = Gallery::getImageList($page, $setting['show_items'], $cat_id);
        } else {
            if ($total > $setting['show_items'])$is_pagination = true;   
            $img_list = Gallery::getImageList($page, $setting['show_items']);
        }
        
        require_once (ROOT . '/template/admin/views/gallery/index.php');
    }


    /**
     * ДОБАВИТЬ ИЗОБРАЖЕНИЕ
     */
    public function actionAddimg()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $params = unserialize(System::getExtensionSetting('gallery'));
        
        if (isset($_POST['addimg']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $title = !empty($_POST['title']) ? htmlentities($_POST['title']) : time();
            $cat_id = intval($_POST['cat_id']);
            $status = intval($_POST['status']);
            $img_alt = htmlentities($_POST['img_alt']);
            $desc = htmlentities($_POST['img_desc']);
            $link = $_POST['link'];
            $add = false;

            if (isset($_FILES['image'])) {
                $count = count($_FILES['image']["name"]);
                $folder = ROOT . '/images/gallery/'; // папка для сохранения
                $thumb_folder = ROOT . '/images/gallery/thumb/';
                
                for($i = 0; $i < $count; $i++) {
                    $tmp_name = $_FILES["image"]["tmp_name"][$i]; // Временное имя картинки на сервере
                    $img = strtolower($_FILES["image"]["name"][$i]); // Имя картинки при загрузке 
                    $thumb_path = $thumb_folder . $img;
                    $path = $folder . $img; // Полный путь с именем файла
                    
                    if (is_uploaded_file($tmp_name)) {
                        $thumb = Gallery::imgResize($tmp_name, $params['params']['thumb_w'], 0, $thumb_path, $params['params']['thumb_q']);
                        $move = move_uploaded_file($tmp_name, $path);
                    }
                    
                    $add = Gallery::addNewImg($title, $cat_id, $img_alt, $desc, $img, $status, $link);
                }
            
                if ($add) {
                    System::redirectUrl("/admin/gallery", $add);
                }
            }

            System::redirectUrl("/admin/gallery/add");
        }
        
        require_once (ROOT . '/template/admin/views/gallery/add.php');
    }


    /**
     * ИЗМЕНИТЬ ИЗОБРАЖЕНИЕ
     * @param $id
     */
    public function actionEditimg($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $params = unserialize(System::getExtensionSetting('gallery'));
        $id = intval($id);
        
        if (isset($_POST['editimg']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $title = !empty($_POST['title']) ? htmlentities($_POST['title']) : time();
            $cat_id = intval($_POST['cat_id']);
            $status = intval($_POST['status']);
            $img_alt = htmlentities($_POST['img_alt']);
            $desc = htmlentities($_POST['img_desc']);
            $link = $_POST['link'];
            
            if (isset($_FILES['image']) && $_FILES["image"]["size"] != 0) {
                $tmp_name = $_FILES["image"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["image"]["name"]; // Имя картинки при загрузке 
                
                $folder = ROOT . '/images/gallery/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                $thumb_folder = ROOT . '/images/gallery/thumb/';
                $thumb_path = $thumb_folder . $img;
                if (is_uploaded_file($tmp_name)) {
                    $thumb = Gallery::imgResize($tmp_name, $params['params']['thumb_w'], 0, $thumb_path, $params['params']['thumb_q']);
                    $move = move_uploaded_file($tmp_name, $path);
                }
            } else {
                $img = $_POST['current_img'];
            }
            
            $edit = Gallery::editImage($id, $title, $cat_id, $img_alt, $desc, $img, $status, $link);
            System::redirectUrl("Location: /admin/gallery/edit/$id", $edit);
        }
        
        $img = Gallery::getImageData($id);
        require_once (ROOT . '/template/admin/views/gallery/edit.php');
    }


    /**
     * СПИСОК КАТЕГОРИЙ
     */
    public static function actionCats()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) {
            System::redirectUrl("/admin");;
        }
        
        $name = $_SESSION['admin_name'];
        $params = unserialize(System::getExtensionSetting('gallery'));
        
        $cat_list = Gallery::getCatList();
        require_once (ROOT . '/template/admin/views/gallery/index_cat.php');
    }


    /**
     * СОЗДАТЬ КАТЕГОРИЮ
     */
    public static function actionAddcat()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $params = unserialize(System::getExtensionSetting('gallery'));
        
        if (isset($_POST['addcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $name = htmlentities($_POST['name']);
            $cat_desc = htmlentities($_POST['cat_desc']);
            $status = intval($_POST['status']);
            $title = isset($_POST['title']) ? htmlentities($_POST['title']) : $name;
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $parent_id = intval($_POST['parent_id']);
            $sort = intval($_POST['sort']);
            $alias = !empty($_POST['alias']) ? htmlentities($_POST['alias']) : System::Translit($name);
            
            if (isset($_FILES['cover'])) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке 
                
                $folder = ROOT . '/images/gallery/cats/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    $move = move_uploaded_file($tmp_name, $path);
                }
            }
            
            $add = Gallery::addCategory($name, $cat_desc, $status, $title, $meta_desc, $meta_keys, $img, $parent_id, $sort, $alias);
            if ($add) {
                header("Location: /admin/gallery/cats?success");
            }
        }
        
        require_once (ROOT . '/template/admin/views/gallery/addcat.php');
    }


    /**
     * ИЗМНЕИТЬ КАТЕГОРИЮ
     * @param $id
     */
    public function actionEditcat($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $params = array();
        $id = intval($id);
        
        $params = unserialize(System::getExtensionSetting('gallery'));
        
        if (isset($_POST['editcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $name = htmlentities($_POST['name']);
            $cat_desc = htmlentities($_POST['cat_desc']);
            $status = intval($_POST['status']);
            $title = !empty($_POST['title']) ? htmlentities($_POST['title']) : $name;
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $parent_id = intval($_POST['parent_id']);
            $sort = intval($_POST['sort']);
            $alias = !empty($_POST['alias']) ? htmlentities($_POST['alias']) : System::Translit($name);
            
            if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке 
                
                $folder = ROOT . '/images/gallery/cats/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    move_uploaded_file($tmp_name, $path);
                }
            } else {
                $img = $_POST['current_img'];
            }
            
            $edit = Gallery::editGalleryCat($id, $name, $cat_desc, $status, $title, $meta_desc, $meta_keys, $img, $parent_id, $sort, $alias);
            System::redirectUrl("Location: /admin/gallery/editcat/$id");
        }
        
        $cat = Gallery::getCatData($id);
        require_once (ROOT . '/template/admin/views/gallery/editcat.php');
    }


    /**
     * УДАЛИТЬ ИЗОБРАЖЕНИЕ
     * @param $id
     */
    public function actionDelimg($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_gallery'])) {
            System::redirectUrl("/admin");
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Gallery::delImg($id);
            System::redirectUrl("/admin/gallery", $del);
        }
    }


    /**
     * УДАЛИТЬ КАТЕГОРИЮ
     * @param $id
     */
    public function actionDeltcat($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Gallery::delCategory($id);
            System::redirectUrl("/admin/gallery/cats", $del);
        }
    }


    /**
     * УДАЛИТЬ РАЗДЕЛ
     * @param $id
     */
    public function actionDelsec($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $id = intval($id);
        
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Gallery::delSection($id);
            System::redirectUrl("/admin/gallery/sections", $del);
        }
    }
    
    
    // НАСТРОЙКА ГАЛЕРЕИ
    public function actionSettings()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_gallery'])) {
            System::redirectUrl("/admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        
        if (isset($_POST['savegallery']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $params = serialize($_POST['gallery']);
            $status = intval($_POST['status']);
            
            $save = Gallery::SaveGallerySetting($params, $status);
            System::redirectUrl('/admin/gallerysettings', $save);
        }
        
        $params = unserialize(System::getExtensionSetting('gallery'));
        $enable = System::getExtensionStatus('gallery');
        require_once (ROOT . '/template/admin/views/gallery/setting.php');
    }
    
    
    // ПРОВЕРКА ЛИЦЕНЗИИ
    public static function checkLicense($params)
    {
        $domain = Helper::getDomain();
		$license = $params['params']['license'];
		if (sha1($domain.':'.self::SECRET) != $license) exit('WOW! License ERROR! Please enter valid license key');
    }
}