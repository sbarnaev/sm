<?php defined('BILLINGMASTER') or die;


class Gallery {
    
    
    // ДОБАВИТЬ ИЗОБРАЖЕНИЕ
    public static function addNewImg($title, $cat_id, $img_alt, $desc, $img, $status, $link)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'gallery_items (cat_id, file, title, item_desc, alt, status, link ) 
                VALUES (:cat_id, :file, :title, :item_desc, :alt, :status, :link)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':file', $img, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':item_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':link', $link, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ ИЗОБРАЖЕНИЕ
    public static function editImage($id, $title, $cat_id, $img_alt, $desc, $img, $status, $link)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'gallery_items SET cat_id = :cat_id, file = :file, title = :title, item_desc = :item_desc,
        alt = :alt, status = :status, link = :link WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':file', $img, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':item_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':link', $link, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ДАННЫЕ ИЗОБРАЖЕНИЯ
    public static function getImageData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."gallery_items WHERE id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ОБЩЕЕ КОЛ-ВО изображений
    public static function totalIMG()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."gallery_items");
        $count = $result->fetch();
        return $count[0];
    }
    
    // СПИСОК ИЗОБРАЖЕНИЙ
    public static function getImageList($page = 1, $show_items = null, $cat_id = null)
    {
        $offset = ($page - 1) * $show_items;
        $db = Db::getConnection();
        if($cat_id == null) $result = $db->query("SELECT * FROM ".PREFICS."gallery_items ORDER BY id DESC LIMIT $show_items OFFSET $offset");
        else $result = $db->query("SELECT * FROM ".PREFICS."gallery_items WHERE cat_id = $cat_id ORDER BY id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['file'] = $row['file'];
            $data[$i]['title'] = $row['title'];
            $data[$i]['status'] = $row['status'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК ИЗОБРАЖЕНИЙ КАТЕГОРИИ
    public static function getImagesByCat($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."gallery_items WHERE cat_id = $id AND status = 1 ORDER BY id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['file'] = $row['file'];
            $data[$i]['title'] = $row['title'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['alt'] = $row['alt'];
            $data[$i]['item_desc'] = $row['item_desc'];
            $data[$i]['link'] = $row['link'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
    // СПИСОК КАТЕГОРИЙ
    public static function getCatList($status = null)
    {
        $db = Db::getConnection();
        if($status != null) $result = $db->query("SELECT * FROM ".PREFICS."gallery_cats WHERE status = 1 ORDER BY sort DESC");
        else $result = $db->query("SELECT * FROM ".PREFICS."gallery_cats ORDER BY sort DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['cat_name'] = $row['cat_name'];
            $data[$i]['cat_cover'] = $row['cat_cover'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['sort'] = $row['sort'];
            $data[$i]['parent_id'] = $row['parent_id'];
            $data[$i]['alias'] = $row['alias'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    public static function getSubCatList($cat_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."gallery_cats WHERE status = 1 AND parent_id = $cat_id ORDER BY sort DESC");
        $i = 0;
        while($row = $result->fetch()){
           $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['cat_name'] = $row['cat_name'];
            $data[$i]['cat_cover'] = $row['cat_cover'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['sort'] = $row['sort'];
            $data[$i]['parent_id'] = $row['parent_id'];
            $data[$i]['alias'] = $row['alias'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
    // ДОБАВИТЬ КАТЕГОРИЮ
    public static function addCategory($name, $cat_desc, $status, $title, $meta_desc, $meta_keys, $img, $parent_id, $sort, $alias)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'gallery_cats (cat_name, cat_cover, cat_desc, cat_title, meta_desc, meta_keys, status, parent_id, sort, alias ) 
                VALUES (:cat_name, :cat_cover, :cat_desc, :cat_title, :meta_desc, :meta_keys, :status, :parent_id, :sort, :alias)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':cat_name', $name, PDO::PARAM_STR);
        $result->bindParam(':cat_cover', $img, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':cat_title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public static function editGalleryCat($id, $name, $cat_desc, $status, $title, $meta_desc, $meta_keys, $img, $parent_id, $sort, $alias)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'gallery_cats SET cat_name = :cat_name, cat_cover = :cat_cover, cat_desc = :cat_desc, 
                cat_title = :cat_title, meta_desc = :meta_desc, meta_keys = :meta_keys, status = :status, parent_id = :parent_id,
                sort = :sort, alias = :alias WHERE cat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':cat_name', $name, PDO::PARAM_STR);
        $result->bindParam(':cat_cover', $img, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':cat_title', $title, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        return $result->execute();
    }
    
    // ДАННЫЕ КАТЕГОРИИ
    public static function getCatData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."gallery_cats WHERE cat_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
    // ДАННЫЕ КАТЕГОРИИ по алиасу
    public static function getCatDataByAlias($alias)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."gallery_cats WHERE alias = '$alias' LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ИМЯ КАТЕГОРИИ ПО ID 
    public static function getCatName($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT cat_name FROM ".PREFICS."gallery_cats WHERE cat_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data['cat_name'];
        else return false;
    }
    
    
    // УДАЛИТЬ ИЗОБРАЖЕНИЕ
    public static function delImg($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query(" SELECT file FROM ".PREFICS."gallery_items WHERE id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)){
            $file = '/images/gallery/'.$data['file'];
            $thumb = '/images/gallery/thumb/'.$data['file'];
                unlink(ROOT . $file);
                unlink(ROOT . $thumb);
        }
        
        $sql = 'DELETE FROM '.PREFICS.'gallery_items WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ КАТЕГОРИЮ
    public static function delCategory($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."gallery_items WHERE cat_id = $id");
        $count = $result->fetch();
        if($count[0]> 0){
            return false;
        } else {
            
            $result = $db->query(" SELECT cat_cover FROM ".PREFICS."gallery_cats WHERE cat_id = $id ");
            $data = $result->fetch(PDO::FETCH_ASSOC);
            if(isset($data) && !empty($data)){
                $file = '/images/gallery/cats/'.$data['cat_cover'];
                unlink(ROOT . $file);
            }
            
            $sql = 'DELETE FROM '.PREFICS.'gallery_cats WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();
        }
    }
    
    
    // УДАЛИТЬ РАЗДЕЛ
    public static function delSection($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(cat_id) FROM ".PREFICS."gallery_cats WHERE section_id = $id");
        $count = $result->fetch();
        if($count[0]> 0){
            return false;
        } else {
            
            $result = $db->query(" SELECT cover FROM ".PREFICS."gallery_sections WHERE id = $id ");
            $data = $result->fetch(PDO::FETCH_ASSOC);
            if(isset($data) && !empty($data)){
                $file = '/images/gallery/cats/'.$data['cat_cover'];
                unlink(ROOT . $file);
            }
            
            $sql = 'DELETE FROM '.PREFICS.'gallery_sections WHERE id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();
        }
    }
    
    
    
    // РЕСАЙЗ 2
    public static function imgResize($img_path, $w, $h, $src, $quality ) // путь до картинки, ширина, высота, путь для сохранения
    {
        $info   = getimagesize($img_path);
        $width  = $info[0];
        $height = $info[1];
        $type   = $info[2];
         
        switch ($type) { 
        	case 1: 
        		$img = imageCreateFromGif($img_path);
        		imageSaveAlpha($img, true);
        		break;					
        	case 2: 
        		$img = imageCreateFromJpeg($img_path);
        		break;
        	case 3: 
        		$img = imageCreateFromPng($img_path); 
        		imageSaveAlpha($img, true);
        		break;
        }
         
        if (empty($w)) {
        	$w = ceil($h / ($height / $width));
        }
        if (empty($h)) {
        	$h = ceil($w / ($width / $height));
        }
         
        $tmp = imageCreateTrueColor($w, $h);
        if ($type == 1 || $type == 3) {
        	imagealphablending($tmp, true); 
        	imageSaveAlpha($tmp, true);
        	$transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127); 
        	imagefill($tmp, 0, 0, $transparent); 
        	imagecolortransparent($tmp, $transparent);    
        }   
         
        $tw = ceil($h / ($height / $width));
        $th = ceil($w / ($width / $height));
        if ($tw < $w) {
        	imageCopyResampled($tmp, $img, ceil(($w - $tw) / 2), 0, 0, 0, $tw, $h, $width, $height);        
        } else {
        	imageCopyResampled($tmp, $img, 0, ceil(($h - $th) / 2), 0, 0, $w, $th, $width, $height);    
        }            
         
        $img = $tmp;
        
        switch ($type) {
        	case 1:
        		imageGif($img, $src);
        		break;			
        	case 2:
        		imageJpeg($img, $src, $quality);
        		break;			
        	case 3:
        		imagePng($img, $src);
        		break;
        }
         
        imagedestroy($img);
        
        
    }
    
    
    // СОХРАНИТЬ НАСТРОЙКИ ГАЛЕРЕИ
    public static function SaveGallerySetting($params, $status)
    {
        $db = Db::getConnection();  
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'gallery'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
}