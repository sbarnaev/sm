<?php defined('BILLINGMASTER') or die;

class catalogController {
    
    
    // ПРОСМОТР КАТАЛОГА
    public function actionCatalog() 
    {
        $setting = System::getSetting();
        if($setting['enable_catalog'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $use_css = 1;
        $is_page = 'catalog';
        
        if(isset($_GET['cat'])){
            $category = htmlentities($_GET['cat']);
            $category = Product::getCatDataByAlias($category); // id категории
            if($category) {
                $category_data = Product::getCatData($category);
                $list_product = Product::getProductInCatalog($category);
                $title = $category_data['cat_title'];
                $h1 = $category_data['cat_name'];
                $meta_desc = $category_data['cat_meta_desc'];
                $meta_keys = $category_data['cat_keys'];
            }
            else {
                require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                exit();  
            }
        } else {
            $title = $setting['catalog_title'];
            $meta_desc = $setting['catalog_desc'];
            $meta_keys = $setting['catalog_keys'];
            $h1 = $setting['catalog_h1'];
            $list_product = Product::getProductInCatalog();   
        }
        
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/product/catalog.php');
        return true;
    }
    
    
    
    // ПРОСМОТР ПРОДУКТА
    public function actionLanding($alias)
    {
        $setting = System::getSetting();
        if($setting['enable_landing'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        // Данные продукта, если ничего нет, то переход на стр. 404
        $product = Product::getProductDataByAlias($alias);
        if(!$product){
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
        
        $product_id = $product['product_id'];
        $title = $product['product_title'];
        $meta_desc = $product['meta_desc'];
        $meta_keys = $product['meta_keys'];
        $use_css = 1;
        $is_page = 'viewproduct';
        
        // Сегментация
        $blog = System::CheckExtensension('blog', 1);
        if($blog):
            $user_id = User::isAuth();
            $no_count = array(1,3);
            if($user_id && !in_array($user_id, $no_count)) {
                $url = htmlentities($_SERVER["REQUEST_URI"]);
                $url = explode("?", $url);
                $url = $url[0];
                $segment = Blog::Segmentation($user_id, $url);
            }
        endif;
        
        
        // СПЛИТ ТЕСТ
        
        $cookie_split = $setting['cookie'].'_split'; // Сформировали имя куки
        
        if(empty($product['product_text2'])){ // Если 2-ого варианта нет
            
            $var = 1;
            $text_lp = $product['product_text1'];
            
            if(isset($_COOKIE["$cookie_split"])){
                $cookie_arr = json_decode($_COOKIE["$cookie_split"], true);
                $cookie_arr[$product_id] = $var;
                setcookie("$cookie_split", json_encode($cookie_arr), time()+3600*24*30*12, '/');
            } else {
                $cookie_arr[$product_id] = $var;
                setcookie("$cookie_split", json_encode($cookie_arr), time()+3600*24*30*12, '/');   
            }
            
        } else { // Если 2-ой вариант есть
            
            // Проверяем наличие куки
            if(isset($_COOKIE["$cookie_split"])){
                
                $cookie_arr = json_decode($_COOKIE["$cookie_split"], true);
                
                if(array_key_exists($product_id, $cookie_arr)){
                    $var = intval($cookie_arr["$product_id"]); // вариант описания
                    $text_lp = $product["product_text$var"];
                } else {
                    $var = rand(1,2); // генерим вариант
                    $cookie_arr[$product_id] = $var; // новое значение для массива
                    $text_lp = $product["product_text$var"];
                    setcookie("$cookie_split", json_encode($cookie_arr), time()+3600*24*30*12, '/');
                    
                    $_SESSION["$cookie_split"] = json_encode($cookie_arr); // дублируем в сессию
                }
                
            } else {
                
                // если куки нет, генерим вариант
                $var = rand(1,2);
                $text_lp = $product["product_text$var"];
                $cookie_arr["$product_id"] = $var;
                setcookie("$cookie_split", json_encode($cookie_arr), time()+3600*24*30*12, '/');
                
                $_SESSION["$cookie_split"] = json_encode($cookie_arr); // дублируем в сессию
            }
            
            
        }
        
        //$cookie_arr = json_decode($_COOKIE["$cookie_split"], true);
        //print_r($cookie_arr);
        
        $variant = "hits_$var";
        
        $hits = Product::updateHits($product['product_id'], $variant, $product["$variant"] + 1);
        
        $text_tmpl = 'text'.$var.'_tmpl';
        $text_heading = 'text'.$var.'_heading';
        if (isset($_GET['viewmodal'])) {
            $page['in_head'] = '<style>#page {padding:5%}</style>';
            $params['params']['commenthead'] = null;
            $page['in_body']= null;
            
            $page['content'] = $text_lp;
            require_once (ROOT . '/template/'.$setting['template'].'/views/static/static_nostyle.php');
            exit(); 
        }
        if($product["$text_tmpl"] == 1) require_once (ROOT . '/template/'.$setting['template'].'/views/product/view.php');
        if($product["$text_tmpl"] == 2) require_once (ROOT . '/template/'.$setting['template'].'/views/product/card.php');
        if($product["$text_tmpl"] == 0) {
            $use_css = 0;
            $no_tmpl = 1;
            $text_head = 'text'.$var.'_head';
            $text_bottom = 'text'.$var.'_bottom';
            
            $in_head = $product["$text_head"];
            $in_bottom = $product["$text_bottom"];
            require_once (ROOT . '/template/'.$setting['template'].'/views/product/no_view.php');
        }
    }
    
    
    
    
    
    
    // ОТЗЫВЫ 
    
    // СПИСОК ОТЗЫВОВ
    public function actionReviews()
    {
        $setting = System::getSetting();
        if($setting['enable_reviews'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $total = Product::countReviews(1);
        $use_css = 1;
        $is_page = 'reviews';
        $reviews_tune = unserialize(base64_decode($setting['reviews_tune']));
        
        $title = $reviews_tune['title'];
        $h1 = $reviews_tune['h1'];
        $meta_desc = $reviews_tune['meta_desc'];
        $meta_keys = $reviews_tune['meta_keys'];
        $list_reviews = Product::getReviews(1, null, null, $setting['show_items'], $page);   
        
        if($total > $setting['show_items']) $is_pagination = true;
        else $is_pagination = false;
        $pagination = new Pagination($total, $page, $setting['show_items']);
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/product/reviews.php');
        return true;
    }
    
    
    // ДОБАВИТЬ ОТЗЫВ
    public function actionAddreview()
    {
        $setting = System::getSetting();
        if($setting['enable_reviews'] == 0) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $max_upload = $setting['max_upload'] * 1024 * 1024;
        $reviews_tune = unserialize(base64_decode($setting['reviews_tune']));
        $use_css = 1;
        $is_page = 'reviews';
        
        $title = 'Добавить отзыв';
        $meta_desc = '';
        $meta_keys = '';
        $now = time();
        
        if(isset($_POST['addreview']) && !empty($_POST['review']) && !empty($_POST['name']) && is_numeric($_POST['time'])){
            
            $_SESSION['review'] = 1;
            $time = intval($_POST['time']);
            if(($now - $time) < 4) exit('Error undefined');
            
            $name = htmlentities($_POST['name']);
            if(isset($_POST['email'])) $email = htmlentities($_POST['email']);
            else $email = null;
            
            if(isset($_POST['site_url'])) $site_url = htmlentities($_POST['site_url']);
            else $site_url = null;
            
            if(isset($_POST['vk_url'])) $vk_url = htmlentities($_POST['vk_url']);
            else $vk_url = null;
            
            if(isset($_POST['fb_url'])) $fb_url = htmlentities($_POST['fb_url']);
            else $fb_url = null;
            
            if(isset($_POST['range'])) $range = intval($_POST['range']);
            else $range = null;
            
            $review = htmlentities($_POST['review']);
            
            $error = false;
            
            
            
            if(isset($_FILES["photo"]["tmp_name"]) && $_FILES["photo"]["size"] > 0){
                
                
                // тип файла 
                if($_FILES["photo"]["type"] != "image/gif" && $_FILES["photo"]["type"] != "image/jpeg" && $_FILES["photo"]["type"] != "image/png" ) 
                $error = 'Не верный тип файла';
                
                if($_FILES["photo"]["size"] > $max_upload) 
                $error .= 'Файл слишком большой';
                        
                   
                $imageinfo = getimagesize($_FILES["photo"]["tmp_name"]);
                if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/png' && $imageinfo['mime'] != 'image/jpeg') 
                $error .= 'Не верный тип файла';
                        
                        
                        if($error == false){
                            
                            $mime = explode("/",$imageinfo['mime']);
                            
                            $tmp_name = $_FILES["photo"]["tmp_name"]; // Временное имя картинки на сервере
                            //$img = $_FILES["photo"]["name"]; // Имя картинки при загрузке 
                            $img = md5($now).'.'.$mime[1];
                            
                            $folder = ROOT . '/images/reviews/'; // папка для сохранения
                            $path = $folder . $img; // Полный путь с именем файла
                            if(is_uploaded_file($tmp_name)){
                                move_uploaded_file($tmp_name, $path);
                            }
                            
                        } 
                
                
                
                
                
                
            } else $img = null;
            
            if($error == false) {
            
            $add = Product::addReview($time, $name, $email, $site_url, $vk_url, $fb_url, $review, $range, $img);
                header("Location: /reviews/add?success");
                $subject = System::Lang('NEW_REVIEW_SUBJ');
                $text = System::Lang('NEW_REVIEW_TEXT');
                $text .= '<p><a href="'.$setting['script_url'].'/admin?key='.$setting['security_key'].'">Перейти</a></p>';
                $send = Email::SendMessageToBlank($setting['admin_email'], 'BM', $subject, $text);   
            } else {
                header("Location: /reviews/add?fail=$error");
            }
            
        }
        
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/product/review_add.php');
        return true;
    }
    
    
    
}