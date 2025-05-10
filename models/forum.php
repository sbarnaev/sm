<?php defined('BILLINGMASTER') or die;

class Forum {
    
    
    
    // СПИСОК СООБЩЕНИЙ
    public static function getTopicMessage($id, $status = 1)
    {
        $db = Db::getConnection();
        if($status == 1) $result = $db->query("SELECT * FROM ".PREFICS."forum_message WHERE topic_id = $id AND status = 1 ORDER BY mess_id ASC");
        else $result = $db->query("SELECT * FROM ".PREFICS."forum_message WHERE topic_id = $id ORDER BY mess_id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['mess_id'] = $row['mess_id'];
            $data[$i]['topic_id'] = $row['topic_id'];
            $data[$i]['user_id'] = $row['user_id'];
            $data[$i]['text'] = $row['text'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['topic_id'] = $row['topic_id'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ПОДСЧЁТ ОТВЕТОВ В ТЕМЕ
    public static function countMessFromTopic($topic_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(mess_id) FROM ".PREFICS."forum_message WHERE topic_id = $topic_id");
        $count = $result->fetch();
        return $count[0];
    }
    
    
    
    
    // ОТПИСКА ЮЗЕРА ОТ ТЕМЫ 
    public static function unsubscribeUserAtTopic($topic_id, $user_id)
    {
        $db = Db::getConnection(); 
        $notif = 0; 
        $sql = 'UPDATE '.PREFICS.'forum_message SET notif = :notif WHERE user_id = :user_id AND topic_id = :topic_id';
        $result = $db->prepare($sql);
        $result->bindParam(':notif', $notif, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ТЕКСТ СООБЩЕНИЯ 
    public static function getMessageText($mess_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."forum_message WHERE mess_id = $mess_id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // ИЗМЕНИТЬ СООБЩЕНИЕ
    public static function updateMessage($mess_id, $message, $status)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'forum_message SET text = :text, status = :status WHERE mess_id = '.$mess_id;
        $result = $db->prepare($sql);
        $result->bindParam(':text', $message, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // НОВОЕ СООБЩЕНИЕ В ТЕМЕ
    public static function AddTopicMessage($user, $topic_id, $message, $notif, $status)
    {
        
        $db = Db::getConnection();
        $time = time();
        $sql = 'INSERT INTO '.PREFICS.'forum_message (topic_id, user_id, text, notif, status, create_date ) 
                VALUES (:topic_id, :user_id, :text, :notif, :status, :create_date)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user, PDO::PARAM_INT);
        $result->bindParam(':notif', $notif, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':create_date', $time, PDO::PARAM_INT);
        $result->bindParam(':text', $message, PDO::PARAM_STR);
        $result->execute();
        
        // Получить ID только что созданного собщения
        $result = $db->query(" SELECT mess_id FROM ".PREFICS."forum_message WHERE topic_id = $topic_id AND user_id = $user AND create_date = $time ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['mess_id'];
        else return false;
    }
    
    
    
    // ПУЛИКАЦИЯ / УДАЛЕНИЕ СООБЩЕНИЙ
    public static function confirmMessage($mess_id, $act)
    {
        $db = Db::getConnection();
        $status = 1;
        if($act == 1){  
            $sql = 'UPDATE '.PREFICS.'forum_message SET status = :status WHERE mess_id = '.$mess_id;
            $result = $db->prepare($sql);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            return $result->execute();
        } else {
            
            $sql = 'DELETE FROM '.PREFICS.'forum_message WHERE mess_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $mess_id, PDO::PARAM_INT);
            return $result->execute();
        }
    }
    
    
    
    /**
     *   ТЕМЫ / ТОПИКИ
     */
    
    // ПОЛУЧИТЬ СПИСОК ТЕХ, КТО ПОДПИСАН НА ТЕМУ
    public static function getSubsListByTopic($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT user_id FROM ".PREFICS."forum_message WHERE notif = 1 AND topic_id = $id");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['user_id'] = $row['user_id'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ДАННЫЕ ТЕМЫ ПО ID 
    public static function getTopicDataByID($id, $status = 0)
    {
        $db = Db::getConnection();
        if($status == 0) $result = $db->query(" SELECT * FROM ".PREFICS."forum_topics WHERE topic_id = $id ");
        else $result = $db->query(" SELECT * FROM ".PREFICS."forum_topics WHERE topic_id = $id AND status = 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if($status == 1){
            // Записать просмотр  
            $hits = $data['hits'] + 1;
            $sql = 'UPDATE '.PREFICS.'forum_topics SET hits = :hits WHERE topic_id = '.$id;
            $result = $db->prepare($sql);
            $result->bindParam(':hits', $hits, PDO::PARAM_INT);
            $result->execute();
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    
    // СПИСОК ТЕМ
    public static function getTopicList($cat_id = 0, $status = 0)
    {
        $db = Db::getConnection();
        if($cat_id == 0 && $status == 0) $result = $db->query("SELECT * FROM ".PREFICS."forum_topics ORDER BY topic_id DESC");
        elseif($cat_id != 0 && $status == 1) $result = $db->query("SELECT * FROM ".PREFICS."forum_topics WHERE cat_id = $cat_id AND status = 1 ORDER BY topic_id DESC");
        $i = 0;
        if($result){
            
            while($row = $result->fetch()){
            $data[$i]['topic_id'] = $row['topic_id'];
            $data[$i]['topic_img'] = $row['topic_img'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['user_id'] = $row['user_id'];
            $data[$i]['topic_title'] = $row['topic_title'];
            $data[$i]['create_date'] = $row['create_date'];
            $data[$i]['last_update'] = $row['last_update'];
            $data[$i]['discussion'] = $row['discussion'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['hits'] = $row['hits'];
            $i++;
            }
            if(isset($data) && !empty($data)) return $data;   
            
        } else return false;
    }
    
    
    
    // ДОБАВИТЬ НОВУЮ ТЕМУ и вернуть её ID 
    public static function addTopic($name, $cat_id, $status, $topic_message, $discuss, $user_id, $topic_img = null)
    {
        $db = Db::getConnection();
        $date = time();
        $sql = 'INSERT INTO '.PREFICS.'forum_topics (cat_id, user_id, topic_title, topic_message, create_date, discussion, status, topic_img ) 
                VALUES (:cat_id, :user_id, :topic_title, :topic_message, :create_date, :discussion, :status, :topic_img)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':topic_title', $name, PDO::PARAM_STR);
        
        $result->bindParam(':topic_img', $topic_img, PDO::PARAM_STR);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $result->bindParam(':topic_message', $topic_message, PDO::PARAM_STR);
        $result->bindParam(':create_date', $date, PDO::PARAM_INT);
        $result->bindParam(':discussion', $discuss, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->execute();
        
        // ПОЛУЧИТЬ ID только что созданной темы
        $result = $db->query(" SELECT topic_id FROM ".PREFICS."forum_topics WHERE cat_id = $cat_id AND user_id = $user_id AND create_date = $date ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['topic_id'];
        else return false;
    }
    
    
    
    // ИЗМЕНИТЬ ТЕМУ
    public static function editTopic($id, $name, $cat_id, $status, $topic_message, $discuss)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'forum_topics SET cat_id = :cat_id, topic_title = :topic_title, topic_message = :topic_message, 
                                            discussion = :discussion, status = :status WHERE topic_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':topic_title', $name, PDO::PARAM_STR);
        $result->bindParam(':topic_message', $topic_message, PDO::PARAM_STR);
        $result->bindParam(':discussion', $discuss, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    public static function changeImgTopic($id, $img)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'forum_topics SET topic_img = :topic_img WHERE topic_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':topic_img', $img, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    
    // ПОДТВЕРДИТЬ ТЕМУ
    public static function confirmTopic($topic_id)
    {
        $db = Db::getConnection();  
        $status = 1;
        $sql = 'UPDATE '.PREFICS.'forum_topics SET status = :status WHERE topic_id = '.$topic_id . ' AND status = 0';
        $result = $db->prepare($sql);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // УДАЛИТЬ ТЕМУ + СООБЩЕНИЯ
    public static function delTopic($id)
    {
        $db = Db::getConnection();
        
        // + удалить сообщения
        $sql = 'DELETE FROM '.PREFICS.'forum_topics WHERE topic_id = :id; DELETE FROM '.PREFICS.'forum_message WHERE topic_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ДАННЫЕ КАТЕГОРИИ ПО АЛИАСУ
    public static function getCatDataByAlias($alias)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."forum_cats WHERE alias = '$alias' AND status = 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
    // ДАННЫЕ КАТЕГОРИИ ПО ID 
    public static function getCatDataByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."forum_cats WHERE cat_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    
    // ПОЛУЧИТЬ СПИСОК ВСЕХ КАТЕГОРИЙ
    // status = 1 - только опубликованные
    // section - id раздела
    public static function getCatList($status = 0, $section = 0)
    {
        $db = Db::getConnection();
        
        if($status == 1 && $section != 0) $result = $db->query("SELECT * FROM ".PREFICS."forum_cats WHERE status = 1 AND section_id = $section ORDER BY sort DESC");
        elseif($status == 0 && $section != 0) $result = $db->query("SELECT * FROM ".PREFICS."forum_cats WHERE section_id = $section ORDER BY sort DESC");
        else $result = $db->query("SELECT * FROM ".PREFICS."forum_cats ORDER BY cat_id DESC");
        $i = 0;
        if($result){
            
            while($row = $result->fetch()){
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['section_id'] = $row['section_id'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['create_date'] = $row['create_date'];
            $data[$i]['access_type'] = $row['access_type'];
            $data[$i]['groups'] = $row['groups'];
            $data[$i]['subs'] = $row['subs'];
            $data[$i]['alias'] = $row['alias'];
            $i++;
            }
            if(isset($data)) return $data;
            
        } else return false;
    }
    
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public static function editCat($id, $name, $alias, $section, $type_access, $groups, $subs, $title, $meta_desc, $meta_keys, $status, $cat_desc)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'forum_cats SET section_id = :section_id, name = :name, title = :title, alias = :alias, cat_desc = :cat_desc, 
                                                metadesc = :metadesc, metakeys = :metakeys, access_type = :access_type, groups = :groups, 
                                                subs = :subs, status = :status WHERE cat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':section_id', $section, PDO::PARAM_INT);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':metadesc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':metakeys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':access_type', $type_access, PDO::PARAM_INT);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':subs', $subs, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ДОБАВИТЬ КАТЕГОРИЮ
    public static function addCat($name, $alias, $section, $type_access, $groups, $subs, $title, $meta_desc, $meta_keys, $status, $cat_desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'forum_cats (section_id, name, title, alias, cat_desc, metadesc, metakeys, access_type, groups, 
                                            subs, status ) 
                VALUES (:section_id, :name, :title, :alias, :cat_desc, :metadesc, :metakeys, :access_type, :groups, 
                                            :subs, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':section_id', $section, PDO::PARAM_INT);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':metadesc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':metakeys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':access_type', $type_access, PDO::PARAM_INT);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':subs', $subs, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // УДАЛИТЬ КАТЕГОРИЮ
    public static function delCategory($id)
    {
        $db = Db::getConnection();
        
        // + удалить все темы и сообщения
        
        $sql = 'DELETE FROM '.PREFICS.'forum_cats WHERE cat_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ СПИСОК РАЗДЕЛОВ
    public static function getForumSections($status = 0)
    {
        $db = Db::getConnection();
        if($status == 0) $result = $db->query("SELECT * FROM ".PREFICS."forum_sections ORDER BY section_id ASC");
        else $result = $db->query("SELECT * FROM ".PREFICS."forum_sections WHERE status = 1 ORDER BY section_id ASC");
        $i = 0;
        if($result){
            while($row = $result->fetch()){
            $data[$i]['section_id'] = $row['section_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['create_date'] = $row['create_date'];
            $data[$i]['status'] = $row['status'];
            $i++;
            } 
            if(isset($data) && !empty($data)) return $data;
			return false;
        } else return false;
    }
    
    
    
    // ПОЛУЧИТЬ ДАННЫЕ РАЗДЕЛА ПО ID 
    public static function getSectionDataByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."forum_sections WHERE section_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
    }
    
    
    // СОЗДАТЬ РАЗДЕЛ
    public static function AddSection($name, $alias, $title, $desc, $metadesc, $metakeys, $status)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'forum_sections (name, alias, title, section_desc, metadesc, metakeys, status ) 
                VALUES (:name, :alias, :title, :section_desc, :metadesc, :metakeys, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':section_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':metadesc', $metadesc, PDO::PARAM_STR);
        $result->bindParam(':metakeys', $metakeys, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ РАЗДЕЛ
    public static function editSection($id, $name, $alias, $title, $desc, $metadesc, $metakeys, $status)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'forum_sections SET name = :name, alias = :alias, title = :title, section_desc = :section_desc, 
                                            metadesc = :metadesc, metakeys = :metakeys, status = :status WHERE section_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':section_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':metadesc', $metadesc, PDO::PARAM_STR);
        $result->bindParam(':metakeys', $metakeys, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    
    // ИМЯ РАЗДЕЛА ПО ID 
    public static function getSectionNameByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT name FROM ".PREFICS."forum_sections WHERE section_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['name'];
        else return false;
    }
    
    
    
    // УДАЛИТЬ РАЗДЕЛ
    public static function delSection($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query("SELECT COUNT(cat_id) FROM ".PREFICS."forum_cats WHERE section_id = $id");
        $count = $result->fetch();
        if($count[0] == 0){
            $sql = 'DELETE FROM '.PREFICS.'forum_sections WHERE section_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();   
        } else return false;
    }
    
    
    
    /**
     *   НАСТРОЙКИ
     */
    
    
    
    // ПОЛУЧИТЬ НАСТРОЙКИ ФОРУМА
    public static function getForumSetting()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT params FROM ".PREFICS."extensions WHERE name = 'forum' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['params'];
    }
    
    
    // ИЗМЕНИТЬ НАСТРОЙКИ ФОРУМА
    public static function SaveForumSetting($params, $status)
    {
        $db = Db::getConnection();  
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'forum'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // Получить статус форума
    public static function getForumStatus()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT enable FROM ".PREFICS."extensions WHERE name = 'forum' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['enable'];
        else return false;
    }
    
}