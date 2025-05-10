<?php defined('BILLINGMASTER') or die;

class Blog {
    
    
    public static function getPostListFromSitemap()
    {
        $time = time();
        $db = Db::getConnection();
        $result = $db->query("SELECT post_id, rubric_id, alias FROM ".PREFICS."blog_posts WHERE status = 1 AND start_date < $time AND end_date > $time ORDER BY post_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['rubric_id'] = $row['rubric_id'];
            $data[$i]['post_id'] = $row['post_id'];
            $data[$i]['alias'] = $row['alias'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    // СПИСОК ПОСТОВ В Админке
    public static function getPostList($page = 1, $show_items = null, $from_id = null)
    {
        $offset = ($page - 1) * $show_items;
        $db = Db::getConnection();
        if($page == null) $result = $db->query("SELECT * FROM ".PREFICS."blog_posts ORDER BY start_date DESC");
        else $result = $db->query("SELECT * FROM ".PREFICS."blog_posts ORDER BY start_date DESC LIMIT $show_items OFFSET $offset");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['post_id'] = $row['post_id'];
            $data[$i]['rubric_id'] = $row['rubric_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['post_img'] = $row['post_img'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['alias'] = $row['alias'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК ОПУБЛИКОВАННЫХ ПОСТОВ}
    public static function getPublicPostList($limit)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."blog_posts WHERE status = 1 ORDER BY post_id DESC LIMIT $limit");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['post_id'] = $row['post_id'];
            $data[$i]['rubric_id'] = $row['rubric_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['post_img'] = $row['post_img'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['alias'] = $row['alias'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК ПОСТОВ ПО ID
    public static function getPostListByID($show_items, $from_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."blog_posts WHERE post_id IN ($from_id) ORDER BY post_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['post_id'] = $row['post_id'];
            $data[$i]['rubric_id'] = $row['rubric_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['post_img'] = $row['post_img'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['alias'] = $row['alias'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК ФИЛЬТРОВАННЫХ ПОСТОВ
    public static function getPostFilterList($title, $cat_id, $status)
    {
        $db = Db::getConnection();
        $sql = '';
        
        if($cat_id != 0) { // выбрана категория
            
            if($status != 2){ // выбран статус
                $sql = "WHERE rubric_id = $cat_id AND status = $status ";
            } else $sql = "WHERE rubric_id = $cat_id "; // не выбран статус
            
        } else { // не выбрана категория
            
            if($status != 2){ // выбран статус
                $sql = "WHERE status = $status ";
            }
        }
        
        if(!empty($title) && !empty($sql)) $sql = $sql ." AND name LIKE '%$title%'";
        elseif(!empty($title) && empty($sql)) $sql = "WHERE name LIKE '%$title%'";
        
        //echo "SELECT * FROM ".PREFICS."blog_posts $sql ORDER BY post_id DESC";
        //exit();
    
        $result = $db->query("SELECT * FROM ".PREFICS."blog_posts $sql ORDER BY post_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['post_id'] = $row['post_id'];
            $data[$i]['rubric_id'] = $row['rubric_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['post_img'] = $row['post_img'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['alias'] = $row['alias'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
    // ПОДСЧИТАТЬ КОЛ_ВО записей всего
    public static function countAllPost($rubric_id = 0, $status = 0)
    {
        $db = Db::getConnection();
        if($status != 0) $where = " WHERE status = 1";
        else $where = '';
        if($rubric_id == 0) $result = $db->query("SELECT COUNT(post_id) FROM ".PREFICS."blog_posts" . $where);
        else $result = $db->query("SELECT COUNT(post_id) FROM ".PREFICS."blog_posts WHERE rubric_id = $rubric_id");
        $count = $result->fetch();
        return $count[0];
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ОПУБЛИКОВАННЫХ ЗАПИСЕЙ
    public static function getPostPublicList($time, $rubric = 0, $page = 1, $show_items, $sort = 'post_id')
    {
        $offset = ($page - 1) * $show_items;
        
        $db = Db::getConnection();
        
        if($rubric != 0){
            $result = $db->query("SELECT * FROM ".PREFICS."blog_posts WHERE rubric_id = $rubric AND status = 1 AND start_date < $time AND end_date > $time ORDER BY $sort DESC LIMIT $show_items OFFSET $offset");
        } else {
            $result = $db->query("SELECT * FROM ".PREFICS."blog_posts WHERE status = 1 AND start_date < $time AND end_date > $time ORDER BY $sort DESC LIMIT $show_items OFFSET $offset");   
        }
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['post_id'] = $row['post_id'];
            $data[$i]['rubric_id'] = $row['rubric_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['post_img'] = $row['post_img'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['intro'] = $row['intro'];
            $data[$i]['alias'] = $row['alias'];
            $data[$i]['img_alt'] = $row['img_alt'];
            $data[$i]['create_date'] = $row['create_date'];
			$data[$i]['start_date'] = $row['start_date'];
            $data[$i]['author_id'] = $row['author_id'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ ЗАПИСИ ПО ID 
    public static function getPostDataByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."blog_posts WHERE post_id = $id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // ЗАПИСАТЬ ХИТ
    public static function writeHit($id, $hits)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'blog_posts SET hits = :hits WHERE post_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':hits', $hits, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ПОЛУЧИТЬ ДАННЫЕ ЗАПИСИ ПО АЛИАСУ ЗАПИСИ и ID РУБРИКИ
    public static function getPostByRubric($rubric_id, $alias)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."blog_posts WHERE status = 1 AND alias = '$alias' AND rubric_id = $rubric_id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ДОБАВИТЬ ЗАПИСЬ
    public static function addPost($name, $rubric_id, $img, $img_alt, $short_desc, $status, $text, $alias, $title, $meta_desc, $meta_keys, 
            $start, $end, $time, $show_cover, $author_id)
    {
        $db = Db::getConnection();
        $hits = 0;
        $sql = 'INSERT INTO '.PREFICS.'blog_posts (rubric_id, name, alias, title, meta_desc, meta_keys, post_img, img_alt, intro, text,
                                                    start_date, end_date, create_date, hits, status, show_cover, author_id ) 
                VALUES (:rubric_id, :name, :alias, :title, :meta_desc, :meta_keys, :post_img, :img_alt, :intro, :text,
                                                    :start_date, :end_date, :create_date, :hits, :status, :show_cover, :author_id)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':rubric_id', $rubric_id, PDO::PARAM_INT);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':post_img', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':intro', $short_desc, PDO::PARAM_STR);
        $result->bindParam(':text', $text, PDO::PARAM_STR);
        
        $result->bindParam(':start_date', $start, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':create_date', $time, PDO::PARAM_INT);
        $result->bindParam(':hits', $hits, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        
        $result->bindParam(':show_cover', $show_cover, PDO::PARAM_INT);
        $result->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ИЗМЕНИТЬ ЗАПИСЬ В БЛОГЕ
    public static function editPost($id, $name, $rubric_id, $img, $img_alt, $short_desc, $status, $text, $alias, $title, $meta_desc, $meta_keys, 
            $start, $end, $show_cover, $custom_code, $author_id, $sort)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'blog_posts SET rubric_id = :rubric_id, name = :name, alias = :alias, title = :title, meta_desc = :meta_desc,
                                                meta_keys = :meta_keys, post_img = :post_img, img_alt = :img_alt, intro = :intro, text = :text,
                                                start_date = :start_date, end_date = :end_date, status = :status, show_cover = :show_cover, 
                                                custom_code = :custom_code, author_id = :author_id, sort = :sort
                                                WHERE post_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':rubric_id', $rubric_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':post_img', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':intro', $short_desc, PDO::PARAM_STR);
        $result->bindParam(':text', $text, PDO::PARAM_STR);
        $result->bindParam(':custom_code', $custom_code, PDO::PARAM_STR);
        $result->bindParam(':show_cover', $show_cover, PDO::PARAM_INT);
        
        $result->bindParam(':start_date', $start, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ ЗАПИСЬ
    public static function delPost($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'blog_posts WHERE post_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ПОЛУЧИТЬ СПИСОК РУБРИК 
    public static function getRubricList($status = null)
    {
        $db = Db::getConnection();
        if($status == null) $result = $db->query("SELECT * FROM ".PREFICS."blog_rubrics ORDER BY id ASC");
        else $result = $db->query("SELECT * FROM ".PREFICS."blog_rubrics WHERE status = 1 ORDER BY id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['alias'] = $row['alias'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ РУБРИКИ ПО ID
    public static function getRubricDataByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."blog_rubrics WHERE id = $id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // ПОЛУЧИТЬ ДАННЫЕ РУБРИКИ ПО АЛИАСУ
    public static function getRubricByAlias($alias)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."blog_rubrics WHERE alias = '$alias' LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // ПОЛУЧИТЬ АЛИАС РУБРИКИ ПО ID 
    public static function getRubricAlias($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT alias FROM ".PREFICS."blog_rubrics WHERE id = $id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['alias'];
        else return false;
    }
    
    
    
    // ДОБАВИТЬ РУБРИКУ 
    public static function addRubric($name, $alias, $title, $short_desc, $status, $meta_desc, $meta_keys)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'blog_rubrics (name, alias, title, meta_desc, meta_keys, short_desc, status ) 
                VALUES (:name, :alias, :title, :meta_desc, :meta_keys, :short_desc, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':short_desc', $short_desc, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ИЗМЕНИТЬ РУБРИКУ
    public static function EditRubric($id, $name, $alias, $title, $short_desc, $status, $meta_desc, $meta_keys, $access_type, $groups, $planes)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'blog_rubrics SET name = :name, alias = :alias, title = :title, meta_desc = :meta_desc, 
                                meta_keys = :meta_keys, short_desc = :short_desc, status = :status, access_type = :access_type, groups = :groups, planes = :planes
                                WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':short_desc', $short_desc, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':groups', $groups, PDO::PARAM_STR);
        $result->bindParam(':planes', $planes, PDO::PARAM_STR);
        $result->bindParam(':access_type', $access_type, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ПОЛУЧИТЬ ИМЯ РУБРИКИ по ID 
    public static function getRubricName($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT name FROM ".PREFICS."blog_rubrics WHERE id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['name'];
        else return false;
    }
    
    
    // УДАЛИТЬ РУБРИКУ
    public static function deleteRubric($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(post_id) FROM ".PREFICS."blog_posts WHERE rubric_id = $id");
        $count = $result->fetch();
        if($count[0] > 0) return false;
        
        $sql = 'DELETE FROM '.PREFICS.'blog_rubrics WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    /**
     *   СЕГМЕНТЫ 
     */
    
    
    // ПОЛУЧИТЬ СПИСОК СЕГМЕНТОВ
    public static function getSegmentsList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."segments ORDER BY sid ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['sid'] = $row['sid'];
            $data[$i]['name'] = $row['name'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // ПОЛУЧИТЬ ДАННЫЕ СЕГМЕНТА
    public static function getSegmentData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."segments WHERE sid = $id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // СОЗДАТЬ СЕГМЕНТ
    public static function addSegment($name, $desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'segments (name, seg_desc ) 
                VALUES (:name, :seg_desc)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':seg_desc', $desc, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // РЕДАКТИРОВАТЬ СЕГМЕНТ
    public static function editSegment($id, $name, $desc)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'segments SET name = :name, seg_desc = :seg_desc WHERE sid = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':seg_desc', $desc, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ СПИСОК URL ДЛЯ СЕГМЕНТА
    public static function getUrllist($sid)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."segments_urls WHERE sid = $sid ORDER BY url_id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['url_id'] = $row['url_id'];
            $data[$i]['sid'] = $row['sid'];
            $data[$i]['url'] = $row['url'];
            $data[$i]['url_desc'] = $row['url_desc'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ДОБАВИТЬ URL для сегмента
    public static function AddURLSegment($sid, $url, $url_desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'segments_urls (sid, url, url_desc ) 
                VALUES (:sid, :url, :url_desc)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':sid', $sid, PDO::PARAM_INT);
        $result->bindParam(':url', $url, PDO::PARAM_STR);
        $result->bindParam(':url_desc', $url_desc, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ СЕГМЕНТ
    public static function delSegment($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'segments WHERE sid = :id; DELETE FROM '.PREFICS.'segments_user_map WHERE sid = :id;
        DELETE FROM '.PREFICS.'segments_urls WHERE sid = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    // УДАЛИТЬ URL у сегмента
    public static function delURLSegment($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'segments_urls WHERE url_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ОБРАБОТКА URL ПРИ СЕГМЕНТАЦИИ
    public static function Segmentation($user_id, $url)
    {
        // Найти url в списке
        $db = Db::getConnection();
        $result = $db->query(" SELECT sid FROM ".PREFICS."segments_urls WHERE url = '$url' LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) {
            $time = time();
            $sid = $data['sid'];
            // Найти запись в карте
            $result = $db->query(" SELECT * FROM ".PREFICS."segments_user_map WHERE user_id = $user_id AND sid = $sid ");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if(isset($row) && !empty($row)) {
                $count = $row['count'] + 1;
                $id = $row['id'];
                // Обновить данные
                $sql = 'UPDATE '.PREFICS.'segments_user_map SET count = :count, last_update = :last_update WHERE id = '.$id;
                $result = $db->prepare($sql);
                $result->bindParam(':count', $count, PDO::PARAM_INT);
                $result->bindParam(':last_update', $time, PDO::PARAM_INT);
                return $result->execute();
            } else {
                // Записать новую строку
                $count = 1;
                $sql = 'INSERT INTO '.PREFICS.'segments_user_map (user_id, sid, count, last_update ) 
                        VALUES (:user_id, :sid, :count, :last_update )';
                
                $result = $db->prepare($sql);
                $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $result->bindParam(':sid', $sid, PDO::PARAM_INT);
                $result->bindParam(':count', $count, PDO::PARAM_INT);
                $result->bindParam(':last_update', $time, PDO::PARAM_INT);
                return $result->execute();
            }
        }
        
    }
    
    
    
    // ПОЛУЧИТЬ СЕГМЕНТЫ ЮЗЕРА
    public static function getUserSegments($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."segments_user_map WHERE user_id = $id ORDER BY sid ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['sid'] = $row['sid'];
            $data[$i]['count'] = $row['count'];
            $data[$i]['last_update'] = $row['last_update'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // НАЗВАНИЕ СЕГМЕНТА ПО ID
    public static function getSegmentName($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT name, seg_desc FROM ".PREFICS."segments WHERE sid = $id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
    }
    
}