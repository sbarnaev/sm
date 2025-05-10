<?php defined('BILLINGMASTER') or die;

class forumController {
    
    
    // ГЛАВНАЯ СТРАНИЦА ФОРУМА
    public function actionIndex()
    {
        
        $setting = System::getSetting();
        $forum = System::CheckExtensension('forum', 1);
        if(!$forum) exit('Forum not installed');
        $params = unserialize(Forum::getForumSetting());
        
        $is_page = 'forum';
        $use_css = 1;
        $group_arr = array(); 
        $plane_arr = array();
        
        $user = intval(User::isAuth());
        if($user){
            
            // Получить данные юзера
            $user_data = User::getUserById($user);
            // Получить группы пользователя
            $group_arr = User::getGroupByUser($user);
            
            // Получить подписки пользователя    
            $membership = System::CheckExtensension('membership', 1);
            if($membership){
                $plane_arr = Member::getPlanesByUser($user);
            }
        }
        
        $title = $params['params']['title'];
        $meta_desc = $params['params']['metadesc'];
        $meta_keys = $params['params']['metakeys'];
        
        // Список опубликованных разделов
        $section_list = Forum::getForumSections(1);
        
        require_once (ROOT . '/template/'.$setting['template'].'/views/forum/index.php');
        return true;
        
    }
    
    
    
    
    // СТРАНИЦА КАТЕГОРИИ
    public function actionCategory($alias)
    {
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        $forum = System::CheckExtensension('forum', 1);
        if(!$forum) exit('Forum not installed');
        $params = unserialize(Forum::getForumSetting());
        
        $alias = htmlentities($alias);
        $is_page = 'forum';
        $use_css = 1;
        $js = 1;
        
        $access = false; // доступа нет по умолчанию
        $group_arr = array();
        $plane_arr = array();
        $user = intval(User::isAuth());
        if($user){
            
            // Получить данные юзера
            $user_data = User::getUserById($user);
            // Получить группы пользователя
            $group_arr = User::getGroupByUser($user);
            
            // Получить подписки пользователя    
            $membership = System::CheckExtensension('membership', 1);
            if($membership){
                $plane_arr = Member::getPlanesByUser($user);
            }
        }
        
        $cat = Forum::getCatDataByAlias($alias);
        $title = $cat['title'];
        $meta_desc = $cat['metadesc'];
        $meta_keys = $cat['metakeys'];
        
        if($cat){
            
            if($cat['access_type'] == 1){ // если доступ по группам
                if($cat['groups'] != null){
                    $group_cat = unserialize($cat['groups']);
                    foreach($group_cat as $group_id){
                        if(in_array($group_id, $group_arr)) $access = true;
                    }
                } else $access = false;
            }
            
            if($cat['access_type'] == 2){ // если досутп по подписке
                if($cat['subs'] != null){
                    $subs_cat = unserialize($cat['subs']);
                    foreach($subs_cat as $subs_id){
                        if(!empty($plane_arr)){
                            if(in_array($subs_id, $plane_arr)) $access = true;    
                        }
                        
                    }
                } else $access = false;
            }
            
            
            
            // СОЗДАНИЕ НОВОЙ ТЕМЫ
            if(isset($_POST['new_topic']) && isset($_COOKIE["$cookie"]) && !empty($_POST['topic_title']) && !empty($_POST['message'])){
                
                $name = htmlentities($_POST['topic_title']);
                $cat_id = $cat['cat_id'];
                if($params['params']['topic_moder'] == 0) $status = 1;
                else $status = 0;
    
                $topic_message = $_POST['message'];
                $discuss = 1;
                
                $topic_img = null;
                if(isset($_POST['topic_img']) && !empty($_POST['topic_img'])) $topic_img = $_POST['topic_img'];
                
                $new = Forum::addTopic($name, $cat_id, $status, $topic_message, $discuss, $user, $topic_img);
                if($new) {
                    header("Location: ".$setting['script_url']."/forum/$alias?success");
                    
                    // Отправить уведомление админу
                    if($params['params']['topic_notif'] != 0 ){
                        
                        if($params['params']['topic_notif'] == 1){
                            Email::SendEmailAboutNewTopic($setting['admin_email'], $name, $cat['alias'], $status, $user, $topic_message, $new);
                            Email::SendEmailAboutNewTopic($setting['support_email'], $name, $cat['alias'], $status, $user, $topic_message, $new);
                        }
                        elseif($params['params']['topic_notif'] == 2) Email::SendEmailAboutNewTopic($setting['admin_email'], $name, $cat['alias'], $status, $user, $topic_message, $new);
                        elseif($params['params']['topic_notif'] == 3) Email::SendEmailAboutNewTopic($setting['support_email'], $name, $cat['alias'], $status, $user, $topic_message, $new);
                        
                    }
                }
                
            }
            
            
            $topic_list = Forum::getTopicList($cat['cat_id'], 1);
            if(isset($user_data) && $user_data['role'] == 'admin') $access = true;
            
            if($access) require_once (ROOT . '/template/'.$setting['template'].'/views/forum/category.php');
            else require_once (ROOT . '/template/'.$setting['template'].'/views/forum/no_access.php');
            return true;   
        
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
    }
    
    
    
    
    // СТРАНИЦА ТЕМЫ И СООБЩЕНИЙ
    public static function actionTopic($alias, $id)
    {
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        $forum = System::CheckExtensension('forum', 1);
        if(!$forum) exit('Forum not installed');
        
        $params = unserialize(Forum::getForumSetting());
        
        $alias = htmlentities($alias);
        $is_page = 'forum';
        $use_css = 1;
        
        $access = false; // доступа нет по умолчанию
        $group_arr = array();
        $plane_arr = array();
        $user = intval(User::isAuth());
        if($user){
            
            // Получить данные юзера
            $user_data = User::getUserById($user);
            
            // Получить группы пользователя
            $group_arr = User::getGroupByUser($user);
            
            // Получить подписки пользователя    
            $membership = System::CheckExtensension('membership', 1);
            if($membership){
                $plane_arr = Member::getPlanesByUser($user);
            }
        }
        
        $title = 'Форум';
        $meta_desc = 'форум';
        $meta_keys = 'форум';
        
        // Данные категории
        $cat = Forum::getCatDataByAlias($alias);
        $topic = Forum::getTopicDataByID($id, 1); // передаёт id топика и статус = 1
        $mess_list = Forum::getTopicMessage($id);
        
        
        // НОВОЕ СООБЩЕНИЕ В ТЕМЕ
        if(isset($_POST['answer']) && !empty($_POST['answer']) && isset($_COOKIE["$cookie"]) && $user == true){
            
            $message = $_POST['message'];
            if(isset($_POST['notif']))$notif = 1;
            else $notif = 0;
            if($params['params']['mess_moder'] == 0) $status = 1;
            else $status = 0;
            
            if(isset($_POST['topic_img']) && !empty($_POST['topic_img'])){
                $change = Forum::changeImgTopic($id, $_POST['topic_img']);
            }
            
            $add_mess = Forum::AddTopicMessage($user, $id, $message, $notif, $status);
            
            if($add_mess) {
                header("Location: ".$setting['script_url']."/forum/$alias/topic-$id");
                
                // Отправить уведомление админу
                if($params['params']['mess_notif'] != 0){
                    
                    if($params['params']['mess_notif'] == 1){
                        
                        Email::SendEmailAdminAboutNewMess($setting['admin_email'], $user, $message, $alias, $id, $add_mess, $status);
                        Email::SendEmailAdminAboutNewMess($setting['support_email'], $user, $message, $alias, $id, $add_mess, $status);
                    }
                    if($params['params']['mess_notif'] == 2) Email::SendEmailAdminAboutNewMess($setting['admin_email'], $user, $message, $alias, $id, $add_mess, $status);
                    if($params['params']['mess_notif'] == 3) Email::SendEmailAdminAboutNewMess($setting['support_email'], $user, $message, $alias, $id, $add_mess, $status);
                }
                
                // Отправить уведомление топикстартеру, если сообщение уже опубликовано
                if($status == 1){
                    if($user != $topic['user_id']){
                    $topicstarter = User::getUserNameByID($topic['user_id']);
                    Email::SendEmailTopicstarterAboutNewMessage($topicstarter['email'], $topicstarter['user_name'], $user, $message, $alias, $id, 1);
                    }
                    
                    // Отправить уведомление всем подписанным   
                    $subs_list = Forum::getSubsListByTopic($id); // Получили список подписанных
                    
                    if($subs_list){
                        foreach($subs_list as $sub){
                            if($topic['user_id'] != $sub['user_id']){
                                $sub_user = User::getUserNameByID($sub['user_id']); // Получили имя и емейл
                                Email::SendEmailTopicstarterAboutNewMessage($sub_user['email'], $sub_user['user_name'], $user, $message, $alias, $id, 0);   
                            }
                        }
                    }
                }
                
            }
            
        }
        
        
        ////  ИЗМЕНИТЬ СООБЩЕНИЕ //////////////////////////////////////////////////////////////////////////////////////
        if(isset($_POST['update']) && isset($_COOKIE["$cookie"])){
            
            $mess_id = intval($_POST['mess_id']);
            $message = Forum::getMessageText($mess_id);
            require_once (ROOT . '/template/'.$setting['template'].'/views/forum/edit_message.php');
            return true;
        }
        
        
        if(isset($_POST['update_mess'])&& isset($_COOKIE["$cookie"])){
            
            $message = $_POST['message'];
            $mess_id = intval($_POST['mess_id']);
            $upd = Forum::updateMessage($mess_id, $message, 1);
            if($upd) header("Location: ".$setting['script_url']."/forum/$alias/topic-$id#mess$mess_id");
            
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////
        
        if($cat && $topic){
            
            if($cat['access_type'] == 1){ // если доступ по группам
                if($cat['groups'] != null){
                    $group_cat = unserialize($cat['groups']);
                    foreach($group_cat as $group_id){
                        if(in_array($group_id, $group_arr)) $access = true;
                    }
                } else $access = 1;
            }
            
            if($cat['access_type'] == 2){ // если досутп по подписке
                if($cat['subs'] != null){
                    $subs_cat = unserialize($cat['subs']);
                    foreach($subs_cat as $subs_id){
                        if(!empty($plane_arr)){
                            if(in_array($subs_id, $plane_arr)) $access = true;
                        }
                    }
                } else $access = 1;
            }
            
            if(isset($user_data) && $user_data['role'] == 'admin') $access = true;
            
            if($access) require_once (ROOT . '/template/'.$setting['template'].'/views/forum/topic.php');
            else require_once (ROOT . '/template/'.$setting['template'].'/views/forum/no_access.php');
            return true;   
        
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
        
    }
    
    
    
    // ПОДТВЕЖДЕНИЕ | УДАЛЕНИЕ НОВОЙ ТЕМЫ
    public function actionConfirmtopic($alias, $topic_id)
    {
        if(isset($_GET['key']) && !empty($_GET['key']) && isset($_GET['public'])){
            $setting = System::getSetting();
            $forum = System::CheckExtensension('forum', 1);
            if(!$forum) exit('Forum not installed'); 
            
                if($_GET['key'] == md5($setting['secret_key'])){
                    
                    $cat = Forum::getCatDataByAlias($alias);
                    if($cat){
                        
                        if($_GET['public'] == 1){
                            $confirm = Forum::confirmTopic($topic_id);
                            if($confirm){
                                header("Location: ".$setting['script_url']."/forum/$alias?success");
                                exit();
                            }   
                        } else {
                            
                            $del = Forum::delTopic($topic_id);
                            if($del){
                                echo '<h1 style="text-align:center">Тема удалена</h1>';
                                exit();
                            }
                            
                        }  
                    }
                    
                    
                } else {
                    require_once (ROOT . '/template/'.$setting['template'].'/404.php');
                    exit();
                }
            
               
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
        
    }
    
    
    
    
    // Подтверждение / УДАЛЕНИЕ НОВОГО СООБЩЕНИЯ В ТЕМЕ
    public static function actionConfirmmess($alias, $topic_id, $mess_id)
    {
        if(isset($_GET['key']) && !empty($_GET['key']) && isset($_GET['public'])){
            
            $setting = System::getSetting();
            $forum = System::CheckExtensension('forum', 1);
            if(!$forum) exit('Forum not installed'); 
            
            if($_GET['key'] == md5($setting['secret_key'])){
                
                $alias = htmlentities($alias);
                $topic_id = intval($topic_id);
                $mess_id = intval($mess_id);
                
                $cat = Forum::getCatDataByAlias($alias);
                    if($cat){
                        
                        if($_GET['public'] == 1){
                            // Публикуем сообщение
                            $confirm = Forum::confirmMessage($mess_id, 1);
                            if($confirm){
                                header("Location: ".$setting['script_url']."/forum/$alias/topic-$topic_id");
                                // Получить данные сообщения и топика
                                $message = Forum::getMessageText($mess_id);
                                $topic = Forum::getTopicDataByID($topic_id);
                                
                                if($message['user_id'] != $topic['user_id']){
                                    $topicstarter = User::getUserNameByID($topic['user_id']);
                                    $alias = Forum::getCatDataByID($topic['cat_id']);
                                    
                                    // Отправить письмо топикстартеру
                                    Email::SendEmailTopicstarterAboutNewMessage($topicstarter['email'], $topicstarter['user_name'], 
                                    $message['user_id'], $message['text'], $alias['alias'], $topic['topic_id'], 1);    
                                    
                                    // Отправить уведомление всем подписанным   
                                    $subs_list = Forum::getSubsListByTopic($topic_id); // Получили список подписанных
                                    
                                    if($subs_list){
                                        foreach($subs_list as $sub){
                                            if($sub['user_id'] != $topic['user_id'] && $sub['user_id'] != $message['user_id']){
                                                $sub_user = User::getUserNameByID($sub['user_id']); // Получили имя и емейл
                                                Email::SendEmailTopicstarterAboutNewMessage($sub_user['email'], $sub_user['user_name'], $message['user_id'], $message['text'], $alias['alias'], $topic['topic_id'], 0);   
                                            }
                                        }
                                    }   
                                }
                                exit();  
                            } 
                            
                        } else {
                            
                            // Удаляем сообщение 
                            $del = Forum::confirmMessage($mess_id, 0);
                            if($del) echo '<h1 style="text-align:center">Сообщение удалено</h1>';
                            exit();
                        }
                        
                    }
                
            }
            
            
        } else {
            require_once (ROOT . '/template/'.$setting['template'].'/404.php');
            exit();
        }
    }
    
    
    
    // ОТПИСКА ОТ НОВЫХ СООБЩЕНИЙ В ТЕМЕ
    public function actionUnsubscribetopic($alias, $topic_id){
        
        $setting = System::getSetting();
        $forum = System::CheckExtensension('forum', 1);
        if(!$forum) exit('Forum not installed'); 
        
        if(isset($_GET['email']) && isset($_GET['key'])){
            
            $alias = htmlentities($alias);
            $topic_id = intval($topic_id);
            
            $key = $_GET['key'];
            $email = $_GET['email'];
            
            if($key == md5($setting['secret_key'])){
                
                $user = User::getUserDataToEmail($email);
                if($user){
                    $unsubs = Forum::unsubscribeUserAtTopic($topic_id, $user['user_id']);
                    if($unsubs){
                        echo '<html><head><meta http-equiv="refresh" content="3;' .$setting['script_url'] . '"></head>
                        <h3 style="text-align:center">Вы успешно отписаны от темы<br />и сейчас будете перенаправлены.</h3></p></html>';
                    }
                }
                
            } else exit('Wrong params');
            
        }
        
    }
    
    
}