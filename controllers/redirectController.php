<?php defined('BILLINGMASTER') or die;


class redirectController {
    
    
    public function actionGo($id)
    {
        $id = intval($id);
        $setting = System::getSetting();
        $now = time();
        $redirect = Redirect::redirectData($id);
        if($redirect){
            
            // ограничение переходов
            if($redirect['limit_hits'] != 0 && $redirect['hits'] >= $redirect['limit_hits']) {
                header("Location: ".$redirect['alt_url']);
                exit();   
            }
            
            // ограничение времени
            if($redirect['end_date'] < $now){
                header("Location: ".$redirect['alt_url']);
                exit();
            }
            
            header("Location: ".$redirect['url']);
            exit();
            
        } else require_once(ROOT . '/template/'.$setting['template'].'/404.php');
    }
    
    
    
}