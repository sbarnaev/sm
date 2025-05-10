<?php defined('BILLINGMASTER') or die;

class siteController {


    /**
     * MAIN PAGE
     * @return bool
     */
    public function actionIndex() {
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();

        // Проверка авторизации
        $userId = User::isAuth();
        if ($userId) {
            $user = User::getUserById($userId);
        }

        $title = $setting_main['main_page_title'];
        $meta_desc = $setting_main['main_page_desc'];
        $meta_keys = $setting_main['main_page_keys'];
        $in_head = $setting['in_head'];
        $in_bottom = $setting['in_body'];
        $use_css = 1;
        $is_page = 'main';

        if ($setting_main['main_page_content'] == 1) { // ОБЫЧНЫЙ МАКЕТ
            require_once (ROOT . "/template/{$setting['template']}/index.php");
        } elseif (in_array($setting_main['main_page_content'], [2, 4])) { // Макет онлайн курсов
            if (!System::CheckExtensension('courses', 1)) {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit;
            }

            $params = unserialize(base64_decode(Course::getCourseSetting()));
            $user = intval(User::isAuth());
            $cat_name = $cats = false;

            if (!isset($_GET['category'])) { // Если в URL нет параметров для категории
                $title = $params['params']['title'];
                $meta_desc = $params['params']['desc'];
                $meta_keys = $params['params']['keys'];
                $h1 = $params['params']['h1'];

                if ($setting_main['main_page_content'] == 4) {
                    $courses = Course::getAllCourseList(1); // Получить все опубликованные курсы вообще
                } else {
                    $cats = Course::getCourseCatFromList(1);
                    $courses = Course::getCourseList(1, 0); // Получить курсы без категории
                }
            } else {
                $alias = htmlentities($_GET['category']);
                $cat_data = Course::getCatDataByAlias($alias); // Получить данные категории по алиасу

                if ($cat_data) {
                    $title = $cat_data['title'];
                    $meta_desc = $cat_data['meta_desc'];
                    $meta_keys = $cat_data['meta_keys'];
                    $cat_name = $cat_data['name'];
                    $courses = Course::getCourseList(1, $cat_data['cat_id']); // Получить курсы данной категории
                } else {
                    require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                    exit;
                }
            }

            require_once (ROOT . "/template/{$setting['template']}/views/course/index.php");
        } elseif ($setting_main['main_page_content'] == 3) { // СТРАНИЦА ВХОДА
            if (!$userId) {
                require_once (ROOT . "/template/{$setting['template']}/views/users/login.php");
            } else {
                $url = System::CheckExtensension('courses', 1) ? '/lk/mycourses' : '/lk';
                System::redirectUrl($url);
            }
        } elseif ($setting_main['main_page_content'] == 5) { // ЗАГРУЗКА ВНЕШНЕГО URL
            $external = $setting_main['external_url'];
            require_once (ROOT . "/template/{$setting['template']}/index.php");
        } elseif ($setting_main['main_page_content'] == 6) { // БЛОГ
            $en_blog = System::CheckExtensension('blog', 1);
            if (!$en_blog) {
                require_once (ROOT . "/template/{$setting['template']}/404.php");
            }

            $now = time();
            $params = unserialize(System::getExtensionSetting('blog'));
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

            $show_items = $params['params']['postcount'];
    		$sort = isset($params['params']['sort']) ? $params['params']['sort'] : 'post_id';
            $post_list = Blog::getPostPublicList($now, 0, $page, $show_items, $sort);
            $total_post = Blog::countAllPost(0, 1);

            if ($total_post > $show_items) {
                $is_pagination = true;
                $pagination = new Pagination($total_post, $page, $show_items);
            } else {
                $is_pagination = false;
            }

            require_once (ROOT . "/template/{$setting['template']}/views/blog/index.php");
        } elseif($setting_main['main_page_content'] == 7 && System::CheckExtensension('training', 1)) {
            require_once (ROOT . "/extensions/training/views/frontend/main_page/training.php");
        }
		
        return true;
    }
    
    
    // СТРАНИЦА ПОЛИТИКИ
    public function actionPolitika()
    {
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();
        $title = $setting_main['politika_link'];
        $meta_desc = null;
        $meta_keys = null;
        $page['in_head'] = '<style>#page {padding:5%}</style>';
        $params['params']['commenthead'] = null;
        $page['in_body']= null;
        
        $page['content'] = $setting_main['politika_text'];
        require_once (ROOT . '/template/'.$setting['template'].'/views/static/static_nostyle.php');
        
    }
    
    
    // СТРАНИЦА ОФЕРТЫ
    public function actionOferta()
    {
        $setting = System::getSetting();
        $setting_main = System::getSettingMainpage();
        $title = $setting_main['oferta_link'];
        $meta_desc = null;
        $meta_keys = null;
        $page['in_head'] = '<style>#page {padding:5%}</style>';
        $params['params']['commenthead'] = null;
        $page['in_body']= null;
        
        $page['content'] = $setting_main['oferta_text'];
        require_once (ROOT . '/template/'.$setting['template'].'/views/static/static_nostyle.php');
    }
    
    
    // СТАТЧИНАЯ СТРАНИЦА
    public function actionPage($alias)
    {
        $setting = System::getSetting();
        $alias = htmlentities($alias);
        
        // Проверка авторизации
        $userId = User::isAuth();
        if($userId) $user = User::getUserById($userId);
        
        $page = System::getPageDataByAlias($alias, 1);
        
        if($page){
            $title = $page['title'];
            $meta_desc = $page['meta_desc'];
            $meta_keys = $page['meta_keys'];
            $use_css = 1;
            $is_page = 'static';
			$curl = $page['curl'];
            
			if(!empty($page['curl'])) require_once (ROOT . '/template/'.$setting['template'].'/views/static/static_curl.php');
            elseif($page['tmpl'] == 1) require_once (ROOT . '/template/'.$setting['template'].'/views/static/static.php');
            else require_once (ROOT . '/template/'.$setting['template'].'/views/static/static_nostyle.php');
            return true;   
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            return true; 
        }
    }
    
    
    
    // СТРАНИЦА АКЦИЙ КРАСНАЯ ЦЕНА
    public function actionSales()
    {
        $setting = System::getSetting();
        $page = Product::getSalesPage();
        $param = null;
        if(!empty($page['param'])) $param = unserialize(base64_decode($page['param']));
        
        // Проверка авторизации
        $userId = User::isAuth();
        if($userId) $user = User::getUserById($userId);
        
        if($param != null) $title = $param['title'];
        else $title = 'Скидки';
        
        if($param != null) $meta_desc = $param['meta_desc'];
        else $meta_desc = 'Товары со скидкой';
        $meta_keys = '';
        
        if($param != null) $h1 = $param['h1'];
        else $h1 = 'Скидки';
        
        $use_css = 1;
        $is_page = 'sale';
        
        $list_product = Product::getSaleProduct();
        
        if($param != null){
            if($param['enable_page'] == 1) require_once (ROOT . '/template/'.$setting['template'].'/views/product/sales.php');
            else require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }
        require_once (ROOT . '/template/'.$setting['template'].'/views/product/sales.php');
        return true; 
    }
    
    
    // ОБРАТНАЯ СВЯЗЬ
    public function actionFeedback()
    {
        $setting = System::getSetting();
        if($setting['enable_feedback'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        if(isset($_GET['id'])){
            $id = intval($_GET['id']);
        } else $id = null;
        
        $form = System::getFormDataByDefault($id);
        if($form){
        $params = unserialize(base64_decode($form['params']));
        
        $title = $params['params']['title'];
        $meta_desc = $params['params']['meta_desc'];
        $meta_keys = $params['params']['meta_keys'];
        $use_css = 1;
        $is_page = 'feedback';
        $site_name = $setting['site_name'];
        $now = time();
        
        if(isset($_POST['feedback']) && is_numeric($_POST['time']) && isset($_SESSION['feedback'])  && isset($_POST['token_sm'])){
            
            if(($now - intval($_POST['time'])) < $params['params']['min_time']) exit('Error send message'); 
			
			$sign = md5($_POST['time'].'+'.$setting['secret_key']);
            if($sign != $_POST['token_sm']) exit('error 911');
            
            $name = null;
            $email = null;
            $phone = null;
            $text = null;
            $field1 = null;
            $field2 = null;
            $field1_name = $params['params']['field1_name'];
            $field2_name = $params['params']['field2_name'];
            
            if(!empty($params['params']['redirect'])) $url = $params['params']['redirect'];
            else {
                if(isset($_GET['id'])) $url = $setting['script_url']."/feedback?success&id=$id";
                else $url = $setting['script_url']."/feedback?success";
            }
            
            if(isset($_POST['name'])) $name = htmlentities($_POST['name']);
            if(isset($_POST['email'])) $email = htmlentities($_POST['email']);
            if(isset($_POST['text'])) $text = htmlentities($_POST['text']);
            
            if(isset($_POST['phone'])) $phone = htmlentities($_POST['phone']);
            if(isset($_POST['field1'])) {
                if(is_array($_POST['field1'])) $field1 = implode(",", $_POST['field1']);
                else $field1 = htmlentities($_POST['field1']);
                }
                
            if(isset($_POST['field2'])) {
                if(is_array($_POST['field2'])) $field2 = implode(",", $_POST['field2']);
                else $field2 = htmlentities($_POST['field2']);
                }
            
            
            if(!empty($params['params']['letter'])){
                
                // Отправляем письмо на указанный емейл 
                if(!empty($params['params']['letter_subj'])) $subj = $params['params']['letter_subj'];
                else $subj = "# Сообщение с сайта $site_name";
                
                $letter = $params['params']['letter'];
                
                $replace = array(
                '[NAME]' => $name,
                '[EMAIL]' => $email,
                '[PHONE]' => $phone,
                );
                
                $user_text = strtr($letter, $replace);
                
                $user_send = Email::SendMessageToBlank($email, $name, $subj, $user_text);
            }
            
            $text_to_admin = "<p>$name отправил(-а) вам сообщение<br />Email: $email<br />Телефон: $phone<br /></p>
            <p>$field1_name = $field1<br />$field2_name = $field2</p>".$text."
            <p><a href='mailto:$email?subject=Re: Ответ на сообщение с сайта $site_name'>Написать ответ</p>";
            
            if(isset($params['params']['recipient']) && !empty($params['params']['recipient']))$admin_recipient = $params['params']['recipient'];
            else $admin_recipient = $setting['admin_email'];
            
            $send = Email::SendMessageToBlank($admin_recipient, $name, "# Сообщение с сайта $site_name", $text_to_admin);
            if($send) {
                
                if($setting['write_feedback'] == 1){
                    $write = System::writeFeedback($name, $email, $phone, $field1, $field2, $text, $form['form_id']);
                    if($write)header("Location: $url ");   
                    else exit('Error');   
                } else {
                    header("Location: $url ");
                    exit();
                }
            }
            
        }
        
        require_once (ROOT . '/template/'.$setting['template'].'/feedback.php');
        } else echo 'Не назначено формы по-умолчанию';
        return true;
    }
    
    
    public function actionSitemap()
    {
        $setting = System::getSetting();
        $menu_items = System::getMenuItemsForSiteMap();
        
        require_once (ROOT . '/template/'.$setting['template'].'/xml_sitemap.php');
        return true;
    }
    
}

