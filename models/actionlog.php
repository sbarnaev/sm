<?php defined('BILLINGMASTER') or die;

class ActionLog {

    /**
     * ЛОГИРОВАНИЕ ДЕЙСТВИЙ В АДМИНКЕ
     * @param $extension
     * @param $method
     * @param $item list: users, products, group, plane, member, post, product, category, installment, map, order, pay
     * @param $item_id
     * @param $date
     * @param $user_id
     * @param $data
     * @return bool
     */
    public static function writeLog($extension, $method, $item, $item_id, $date, $user_id = 0, $data)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'action_log (extension, method, item, item_id, date, user_id, data ) 
                VALUES (:extension, :method, :item, :item_id, :date, :user_id, :data)';

        $result = $db->prepare($sql);
        $result->bindParam(':extension', $extension, PDO::PARAM_STR);
        $result->bindParam(':method', $method, PDO::PARAM_STR);
        $result->bindParam(':item', $item, PDO::PARAM_STR);
        $result->bindParam(':data', $data, PDO::PARAM_STR);
        $result->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':date', $date, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $filter
     * @return mixed
     */
    public static function getActionLogTotal($filter) {
        $where = '';
        if ($filter['is_filter']) {
            $clauses = [];

            if ($filter['extension']) {
                $clauses[] = "extension = '{$filter['extension']}'";
            }
            if ($filter['start_date']) {
                $clauses[] = "date >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "date < {$filter['finish_date']}";
            }
            $where = 'WHERE '. implode(' AND ', $clauses);
        }

        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."action_log $where");
        $data = $result->fetch();

        return $data[0];
    }


    /**
     * ПОЛУЧИТЬ ЛОГ ДЕЙСТВИЙ В АДМИНКЕ
     * @param $filter
     * @param null $page
     * @param null $show_items
     * @return array|bool
     */
    public static function getActionLog($filter, $page = null, $show_items = null)
    {
        $where = '';
        if ($filter['is_filter']) {
            $clauses = [];

            if ($filter['extension']) {
                $clauses[] = "extension = '{$filter['extension']}'";
            }
            if ($filter['start_date']) {
                $clauses[] = "date >= {$filter['start_date']}";
            }
            if ($filter['finish_date']) {
                $clauses[] = "date < {$filter['finish_date']}";
            }
            $where = 'WHERE '. implode(' AND ', $clauses);
        }

        $sql = "SELECT * FROM ".PREFICS."action_log $where ORDER BY id DESC";
        if ($page && $show_items) {
            $offset = ($page - 1) * $show_items;
            $sql .= " LIMIT $show_items OFFSET $offset";
        }

        $db = Db::getConnection();
        $result = $db->query($sql);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ЗАПИСИ В ЛОГЕ ДЕЙСТВИЙ
     * @param $id
     * @return bool|mixed
     */
    public static function getActionLogView($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."action_log WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * УДАЛЕНИЕ СТАРЫХ ЛОГОВ
     * @param $date
     * @return bool
     */
    public static function delOldLogs($date) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'action_log WHERE date < :date');
        $result->bindParam(':date', $date, PDO::PARAM_INT);

        return $result->execute();
    }
}