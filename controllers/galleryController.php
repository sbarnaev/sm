<?php defined('BILLINGMASTER') or die; 

class galleryController {
    
    
    // ГЛАВНАЯ СТРАНИЦА ГАЛЕРЕИ
    public function actionIndex()
    {
        $setting = System::getSetting(); 
        $gallery = System::CheckExtensension('gallery', 1);
        if(!$gallery) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $params = unserialize(System::getExtensionSetting('gallery'));
        $title = $params['params']['title'];
        $meta_desc = $params['params']['desc'];;
        $meta_keys = $params['params']['keys'];;
        $use_css = 1;
        $is_page = 'gallery';
        
        $cat_list = Gallery::getCatList(1);
        require_once (ROOT . '/template/'.$setting['template']. '/views/gallery/index.php');
        return true;
    }
    
    
    
    public function actionCats($alias)
    {
        $setting = System::getSetting(); 
        $gallery = System::CheckExtensension('gallery', 1);
        if(!$gallery) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $params = unserialize(System::getExtensionSetting('gallery'));
        $alias = htmlentities($alias);
        $cat = Gallery::getCatDataByAlias($alias);
        if(!$cat) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
        
        $title = $cat['cat_title'];
        $meta_desc = $cat['meta_desc'];;
        $meta_keys = $cat['meta_keys'];;
        $use_css = 1;
        $is_page = 'gallery';
        define('BM_GALLERY', $params['params']['style']);
        
        $subcat_list = Gallery::getSubCatList($cat['cat_id']);
        $img_list = Gallery::getImagesByCat($cat['cat_id']);
        require_once (ROOT . '/template/'.$setting['template']. '/views/gallery/cat.php');
        return true;
    }
    
    
    public function actionSubcats($alias, $sub_alias)
    {
        $setting = System::getSetting(); 
        $gallery = System::CheckExtensension('gallery', 1);
        if(!$gallery) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
        }
        
        $params = unserialize(System::getExtensionSetting('gallery'));
        $alias = htmlentities($alias);
        $sub_alias = htmlentities($sub_alias);
        
        $cat = Gallery::getCatDataByAlias($alias);
        $sub_cat = Gallery::getCatDataByAlias($sub_alias);
        
        if(!$cat || !$sub_cat) {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
        
        $title = $sub_cat['cat_title'];
        $meta_desc = $sub_cat['meta_desc'];;
        $meta_keys = $sub_cat['meta_keys'];;
        $use_css = 1;
        $is_page = 'gallery';
        define('BM_GALLERY', $params['params']['style']);
        
        $img_list = Gallery::getImagesByCat($sub_cat['cat_id']);
        require_once (ROOT . '/template/'.$setting['template']. '/views/gallery/subcat.php');
        return true;
    }
    
    
    
}