<?php defined('BILLINGMASTER') or die;


class TrainingCategory {

    use ResultMessage;


    /**
     * СПИСОК КАТЕГОРИЙ
     * @param bool $sub_cat
     * @return array|bool
     */
    public static function getCatList($sub_cat = true)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."training_cats";
        $query .= (!$sub_cat ? ' WHERE parent_cat = 0' : '') . ' ORDER BY sort ASC';
        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ДОБАВИТЬ КАТЕГОРИЮ
     * @param $name
     * @param $status
     * @param $alias
     * @param $title
     * @param $cat_desc
     * @param $meta_desc
     * @param $meta_keys
     * @param $img
     * @param $img_alt
     * @param $parent_cat
     * @param $sort
     * @return bool
     */
    public static function addCategory($name, $status, $alias, $title, $cat_desc, $meta_desc, $meta_keys, $img, $img_alt, $parent_cat, $sort)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'training_cats (name, alias, title, meta_desc, meta_keys, cat_desc, status, cover, img_alt, parent_cat, sort ) 
                VALUES (:name, :alias, :title, :meta_desc, :meta_keys, :cat_desc, :status, :img, :img_alt, :parent_cat, :sort)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':img', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':parent_cat', $parent_cat, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * ИЗМЕНИТЬ КАТЕГОРИЮ
     * @param $cat_id
     * @param $name
     * @param $status
     * @param $alias
     * @param $title
     * @param $cat_desc
     * @param $meta_desc
     * @param $meta_keys
     * @param $img
     * @param $img_alt
     * @param $parent_cat
     * @param $sort
     * @return bool
     */
    public static function editCategory($cat_id, $name, $status, $alias, $title, $cat_desc, $meta_desc, $meta_keys, $img, $img_alt, $parent_cat, $sort)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'training_cats SET name = :name, status = :status, alias = :alias, title = :title, cat_desc = :cat_desc, meta_desc = :meta_desc, 
                                                meta_keys = :meta_keys, cover = :img, img_alt = :img_alt, parent_cat = :parent_cat, sort = :sort WHERE cat_id = '.$cat_id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':title', $title, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':img', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':parent_cat', $parent_cat, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ ПО ID
     * @param $cat_id
     * @return bool|mixed
     */
    public static function getCategory($cat_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."training_cats WHERE cat_id = $cat_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИЙ ПО ID КАТЕГОРИЙ
     * @param $cat_ids
     * @return bool|mixed
     */
    public static function getSubCategoriesByParentsIds($cat_ids = array())
    {
        $cat_ids = implode(',', $cat_ids);
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."training_cats WHERE parent_cat IN ($cat_ids) ORDER BY sort ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ ПО АЛИАСУ
     * @param $cat_alias
     * @param int $status
     * @return bool|mixed
     */
    public static function getCategoryByAlias($cat_alias, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."training_cats WHERE alias = :cat_alias";
        $sql .= ($status !== null ? " AND status = $status": '') . ' LIMIT 1';

        $result = $db->prepare($sql);
        $result->bindParam(':cat_alias', $cat_alias, PDO::PARAM_STR);

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ПОДКАТЕГОРИИ
     * @param $cat_id
     * @param int $status
     * @return array|bool
     */
    public static function getSubCategories($cat_id, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."training_cats WHERE parent_cat = :parent_cat";
        $sql .= ($status !== null ? " AND status = $status" : '') . ' ORDER BY sort ASC';

        $result = $db->prepare($sql);
        $result->bindParam(':parent_cat', $cat_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * УДАЛИТЬ КАТЕГОРИЮ
     * @param $cat_id
     * @return bool
     */
    public static function DelCat($cat_id)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."training_cats WHERE parent_cat = $cat_id";
        $result = $db->query($sql);
        $hasparent = $result->fetch(PDO::FETCH_ASSOC);
        if (empty($hasparent)) {
            $sql = 'DELETE FROM '.PREFICS.'training_cats WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $cat_id, PDO::PARAM_INT);
            return $result->execute();
        } else {
            return false;
        }
      
    }


    /**
     * ПОЛУЧИТЬ СОРТИРОВКУ ДЛЯ ДОБАВЛЯЕМОЙ КАТЕГОРИИ
     * @return bool|mixed
     */
    public static function getFreeSort() {
        $db = Db::getConnection();
        $result = $db->query("SELECT MAX(sort) FROM ".PREFICS.'training_cats');
        $count = $result->fetch();

        return (int)$count[0] + 1;
    }
}