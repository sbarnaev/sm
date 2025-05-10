<?php defined('BILLINGMASTER') or die;

class Redirect {
    
    
    // ДАННЫЕ РЕДИРЕКТА ДЛЯ ФРОНТА
    public static function redirectData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT url, alt_url, hits, limit_hits, end_date FROM ".PREFICS."redirect WHERE status = 1 AND id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) {
            
            $hits = $data['hits'] + 1;
            $sql = 'UPDATE '.PREFICS.'redirect SET hits = :hits WHERE id = '.$id;
            $result = $db->prepare($sql);
            $result->bindParam(':hits', $hits, PDO::PARAM_INT);
            $result->execute();
            return $data;
           
        } else return false;
    }
    
    
    
    // СПИСОК РЕДИРЕКТОВ
    public static function getRedirectList($page, $show_items, $cat_id, $url)
    {
        
        $offset = ($page - 1) * $show_items;
        
        $db = Db::getConnection();
        
        if($cat_id != null && $url != null) {
            $url = "%$url%";
            $result = $db->query("SELECT * FROM ".PREFICS."redirect WHERE cat_id = $cat_id AND url LIKE '$url' OR alt_url LIKE '$url' ORDER BY id DESC");
        }
        elseif($cat_id != null && $url == null) $result = $db->query("SELECT * FROM ".PREFICS."redirect WHERE cat_id = $cat_id ORDER BY id DESC");
        elseif($cat_id == null && $url != null) {
            $url = "%$url%";
            $result = $db->query("SELECT * FROM ".PREFICS."redirect WHERE url LIKE '$url' ORDER BY id DESC");
        }
        else $result = $db->query("SELECT * FROM ".PREFICS."redirect ORDER BY id DESC LIMIT ". $show_items. " OFFSET $offset");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['title'] = $row['title'];
            $data[$i]['rdr_desc'] = $row['rdr_desc'];
            $data[$i]['url'] = $row['url'];
            $data[$i]['alt_url'] = $row['alt_url'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['status'] = $row['status'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    
    // СОЗДАТЬ РЕДИРЕКТ
    public static function addRedirect($title, $cat_id, $rdr_desc, $url, $alt_url, $status, $limit, $end, $hits, $now)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'redirect (cat_id, title, rdr_desc, url, alt_url, hits, limit_hits, end_date, create_date, status ) 
                VALUES (:cat_id, :title, :rdr_desc, :url, :alt_url, :hits, :limit_hits, :end_date, :create_date, :status )';
        
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':rdr_desc', $rdr_desc, PDO::PARAM_STR);
        $result->bindParam(':url', $url, PDO::PARAM_STR);
        $result->bindParam(':alt_url', $alt_url, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':hits', $hits, PDO::PARAM_INT);
        $result->bindParam(':limit_hits', $limit, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        $result->bindParam(':create_date', $now, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ИЗМЕНИТЬ РЕДИРЕКТ
    public static function editRedirect($id, $title, $cat_id, $rdr_desc, $url, $alt_url, $status, $limit, $end)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'redirect SET cat_id = :cat_id, title = :title, rdr_desc = :rdr_desc, url = :url, alt_url = :alt_url, 
                            limit_hits = :limit_hits, end_date = :end_date, status = :status WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':rdr_desc', $rdr_desc, PDO::PARAM_STR);
        $result->bindParam(':url', $url, PDO::PARAM_STR);
        $result->bindParam(':alt_url', $alt_url, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':limit_hits', $limit, PDO::PARAM_INT);
        $result->bindParam(':end_date', $end, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // ПОЛУЧИТЬ ДАННЫЕ РЕДИРЕКТА ПО ID для админки
    public static function getRedirectData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."redirect WHERE id = '$id' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК КАТЕГОРИЙ
    public static function getRdrCatList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."redirect_cats ORDER BY cat_id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['cat_desc'] = $row['cat_desc'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ДАННЫЕ КАТЕГОРИИ
    public static function getCat($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."redirect_cats WHERE cat_id = '$id' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
    }
    
    
    
    // СОЗДАТЬ КАТЕГОРИЮ
    public static function addCat($name, $cat_desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'redirect_cats (name, cat_desc ) 
                VALUES (:name, :cat_desc)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public static function editCat($id, $name, $desс)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'redirect_cats SET name = :name, cat_desc = :cat_desc WHERE cat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $desс, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ РЕДИРЕКТ
    public static function delRedirect($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'redirect WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ КАТЕГОРИЮ
    public static function delCat($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(*) FROM ".PREFICS."redirect WHERE cat_id = $id");
        $count = $result->fetch();
        if($count[0] == 0){
            $sql = 'DELETE FROM '.PREFICS.'redirect_cats WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();   
        } else return false;
    }
    
    
    // ВСЕГО РЕДИРЕКТОВ
    public static function totalRedirect()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."redirect");
        $count = $result->fetch();
        return $count[0];
    }
    
    
}