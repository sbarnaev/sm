<?php defined('BILLINGMASTER') or die; 


class blogController {


    /**
     * ГЛАВНАЯ СТРАНИЦА БЛОГА
     */
    public function actionIndex()
    {
        $setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if (!$blog) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }

        $params = unserialize(System::getExtensionSetting('blog'));
        $title = $params['params']['title'];
        $meta_desc = $params['params']['desc'];
        $meta_keys = $params['params']['keys'];
        $h1 = $params['params']['h1'];
        $use_css = 1;
        $is_page = 'blog';
        $now = time();
        
        /*  Pagination  */
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        
        $show_items = $params['params']['postcount'];
		$sort = isset($params['params']['sort']) ? $params['params']['sort'] : 'post_id';
		
        $total_post = Blog::countAllPost(0, 1);
        
        if ($total_post > $show_items) {
            $is_pagination = true;
            $pagination = new Pagination($total_post, $page, $show_items);
        } else {
            $is_pagination = false;
        }
        
        $post_list = Blog::getPostPublicList($now, 0, $page, $show_items, $sort);
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/blog/index.php');
    }
    
    
    
    // СТРАНИЦА КАТЕГОРИИ БЛОГА
    public function actionRubric($alias)
    {
        $setting = System::getSetting();
        $params = unserialize(System::getExtensionSetting('blog'));
        $blog = System::CheckExtensension('blog', 1);
        if (!$blog) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }
		
		$user_groups = false;
        $user_planes = false;
        $sort = isset($params['params']['sort']) ? $params['params']['sort'] : 'post_id';
		
        $alias = htmlentities($alias);
        $rubric = Blog::getRubricByAlias($alias);
		$user_id = User::isAuth();
        
        if ($rubric) {
            $use_css = 1;
            $is_page = 'blog';
            $title = $rubric['title'];
            $meta_desc = $rubric['meta_desc'];
            $meta_keys = $rubric['meta_keys'];
			
			if ($rubric['access_type'] > 0) {
                $access = false;
                if ($user_id) {
                    if ($rubric['access_type'] == 1) {
                        $user_groups = User::getGroupByUser($user_id);
                        $groups_arr = json_decode($rubric['groups'], true);

                        if ($user_groups) {
                            foreach($user_groups as $group) {
                                if (in_array($group, $groups_arr)) $access = true;
                            }
                        }
                    } elseif ($rubric['access_type'] == 2) {
                        $membership = System::CheckExtensension('membership', 1);
                        if ($membership) {
                            $user_planes = Member::getPlanesByUser($user_id);
                            
                            $planes_arr = json_decode($rubric['planes'], true);
                            if ($user_planes) {
                                foreach($user_planes as $plane) {
                                    if (in_array($plane, $planes_arr)) $access = true;
                                }
                            }
                        }      
                    }
                }
            } else {
			    $access = true;
            }
            
            /*  Pagination  */
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $show_items = $params['params']['postcount'];
            $total_post = Blog::countAllPost($rubric['id']);
            
            if ($total_post > $show_items) {
                $is_pagination = true;
                $pagination = new Pagination($total_post, $page, $show_items);
            } else {
                $is_pagination = false;
            }
            
            $now = time();
            $post_list = Blog::getPostPublicList($now, $rubric['id'], $page, $show_items, $sort);
            
            if ($access) {
                require_once (ROOT . '/template/'.$setting['template'].'/views/blog/category.php');
            } else {
                require_once (ROOT . '/template/'.$setting['template'].'/views/blog/no_access.php');
            }
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }
    }


    /**
     * СТРАНИЦА ЗАПИСИ
     * @param $rubric
     * @param $alias
     */
    public function actionPost($rubric, $alias)
    {
        $setting = System::getSetting();
        $blog = System::CheckExtensension('blog', 1);
        if (!$blog) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }

        $params = unserialize(System::getExtensionSetting('blog'));
        $comments = $params['params']['comments'] == 1 ? 1 : false;
        
        $use_css = 1;
        $is_page = 'blog';
        
        // Сегментация
        $user_id = User::isAuth();
        $no_count = array(10,3);
        if ($user_id && !in_array($user_id, $no_count)) {
            $url = htmlentities($_SERVER["REQUEST_URI"]);
            $url = explode("?", $url);
            $url = $url[0];
            $segment = Blog::Segmentation($user_id, $url);
        }
        
        
        $rubric = htmlentities($rubric);
        $alias = htmlentities($alias);
        
        $rubric_data = Blog::getRubricByAlias($rubric);
        if (!$rubric_data) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        } else {
			if ($rubric_data['access_type'] > 0) {
                $access = false;
                if ($user_id) {
                    if ($rubric_data['access_type'] == 1) {
                        $user_groups = User::getGroupByUser($user_id);
                        
                        $groups_arr = json_decode($rubric_data['groups'], true);
                        if ($user_groups) {
                            foreach($user_groups as $group) {
                                if (in_array($group, $groups_arr)) $access = true;
                            }
                        }
                    }
                    
                    if ($rubric_data['access_type'] == 2) {
                        $membership = System::CheckExtensension('membership', 1);
                        if ($membership) {
                            $user_planes = Member::getPlanesByUser($user_id);
                            $planes_arr = json_decode($rubric_data['planes'], true);

                            if ($user_planes) {
                                foreach($user_planes as $plane) {
                                    if (in_array($plane, $planes_arr)) $access = true;
                                }
                            }
                        }      
                    }
                }
            } else {
			    $access = true;
            }
		}
        
        $post = Blog::getPostByRubric($rubric_data['id'], $alias);
        if ($post) {
            $title = $post['title'];
            $meta_desc = $post['meta_desc'];
            $meta_keys = $post['meta_keys'];
            
            $hit = Blog::writeHit($post['post_id'], $post['hits'] + 1);
            
            if ($access) {
                require_once (ROOT . '/template/'.$setting['template'].'/views/blog/post.php');
            } else {
                require_once (ROOT . '/template/'.$setting['template'].'/views/blog/no_access.php');
            }
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
        }
    }
}