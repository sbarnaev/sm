<?php defined('BILLINGMASTER') or die; 


class Stat {

    /**
     * ПОДСЧИТАТЬ ЗАКАЗЫ ЗА ПЕРИОД
     * @param int $start
     * @param int $end
     * @return array
     */
    public static function CountOrders($start = 0, $end = 0)//1541176566 - можно указать дату старта и завершения
    {
        if ($end == 0) {
            $end = time();
        }
    
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE payment_date > $start AND payment_date < $end AND status = 1 AND summ > 0");
        $count_pay = $result->fetch();
    
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE order_date > $start AND order_date < $end AND status != 1");
        $count_nopay = $result->fetch();
    
        $result = $db->query("SELECT SUM(price) FROM ".PREFICS."order_items WHERE order_id IN ( SELECT order_id FROM ".PREFICS."orders WHERE status = 1 AND payment_date > $start AND payment_date < $end AND summ > 0)");
        $summ = $result->fetch();
    
        $result = $db->query("SELECT SUM(price) FROM ".PREFICS."order_items WHERE order_id IN ( SELECT order_id FROM ".PREFICS."orders WHERE status != 1 AND order_date > $start AND order_date < $end AND summ > 0)");
        $nosumm = $result->fetch();
    
        return [
            'pay' => $count_pay[0],
            'nopay' => $count_nopay[0],
            'summ' => $summ[0],
            'nosumm' => $nosumm[0],
        ];
    }
    
    
	
	// СПИСОК ЗАПРОСОВ ПЛАТЁЖНЫХ СИСТЕМ
    public static function getPayLog($start = null, $finish = null, $subs = null, $email = null)
    {
        $db = Db::getConnection();
		
		if($start != null || $finish != null || $subs != null || $email != null) $where = ' WHERE ';
		else $where = false;
		
		if($subs != null && $start == null) $subs = " subs_id = '$subs' ";
		elseif($subs != null && $start != null) $subs = " AND subs_id = '$subs' ";
		
		if($email != null && $subs != null) $email = " AND query LIKE '%$email%' ";
		elseif($email != null && $subs == null) $email = " query LIKE '%$email%' ";
		
        if($start != null){
            
            if($finish == null) $finish = time();
            $sql = "SELECT * FROM ".PREFICS."payment_log WHERE transaction_date > $start AND transaction_date < $finish $subs ORDER BY id DESC LIMIT 1000";
            
        } else $sql = "SELECT * FROM ".PREFICS."payment_log $where $subs $email ORDER BY id DESC LIMIT 1000";
        
        $result = $db->query($sql);
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }
    
    
    // ПРОСМОТР ЗАПРОСА ОТ ПЛАТЁЖНОЙ СИСТЕМЫ
    public static function getPayLogItem($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."payment_log WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)) return $data;
        else return false;
    }
	
	
	
    // СТАТИСТИКА ПО ПРОДУКТАМ
    public static function getProductStat($order, $start=null, $finish=null)
    {
        $db = Db::getConnection();
        if($order == 'summ') $order = 'summ';
        else $order = 'count';
        if ($start&&$finish) {
            $result = $db->query("
            SELECT t1.order_id, t2.product_id, t2.product_name, t2.type_id, SUM(t2.price) as summ, COUNT(t2.product_id) 
            as count from ".PREFICS."orders as t1 LEFT JOIN ".PREFICS."order_items as t2 ON t1.order_id = t2.order_id where
            t1.order_date > $start AND t1.order_date < $finish GROUP BY t1.order_id, t2.product_id, t2.product_name, t2.type_id ORDER BY $order DESC"); 
        } else {
            $result = $db->query("SELECT product_id, product_name, type_id, SUM(price) as summ, COUNT(product_id) as count FROM ".PREFICS."order_items 
                WHERE status = 1 GROUP BY product_id, product_name, type_id ORDER BY $order DESC"); 
        }
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['product_name'] = $row['product_name'];
            $data[$i]['type_id'] = $row['type_id'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['count'] = $row['count'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // СТАТИСТИКА ПО ГРУППАМ КАНАЛОВ
    public static function getGroupStat($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(summ) FROM ".PREFICS."channels WHERE group_id = $id");
        $count = $result->fetch();
        $data['summ'] = $count[0];
        
        $result = $db->query("SELECT SUM(hits) FROM ".PREFICS."channels WHERE group_id = $id");
        $count = $result->fetch();
        $data['hits'] = $count[0];
        
        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."users WHERE channel_id IN ( SELECT channel_id FROM ".PREFICS."channels WHERE group_id = $id)");
        $count = $result->fetch();
        $data['users'] = $count[0];
        
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE status = 1 AND channel_id IN ( SELECT channel_id FROM ".PREFICS."channels WHERE group_id = $id)");
        $count = $result->fetch();
        $data['pay'] = $count[0];
        
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE status = 0 AND channel_id IN ( SELECT channel_id FROM ".PREFICS."channels WHERE group_id = $id)");
        $count = $result->fetch();
        $data['nopay'] = $count[0];
        
        $result = $db->query("SELECT SUM(price) FROM ".PREFICS."order_items WHERE order_id IN ( 
        SELECT order_id FROM ".PREFICS."orders WHERE status = 1 AND channel_id IN ( SELECT channel_id FROM ".PREFICS."channels WHERE group_id = $id )
        ) ");
        $summ = $result->fetch();
        $data['amount'] = $summ[0];
        
        return $data;
    }
    
    
    
    // ПОДСЧИТАТЬ ПОЛЬЗОВАТЕЛЕЙ ЗА ПЕРИОД
    public static function countUsers($start = 1541176566, $end = 0)
    {
        $db = Db::getConnection();
        if($end == 0) $end = time();
        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."users WHERE status = 1 AND reg_date > $start AND reg_date < $end");
        $count = $result->fetch();
        return $count[0];
    }
    
    
    
    // ПОСЧИТАТЬ ЗАКАЗЫ ПО КАНАЛАМ
    public static function getOrderByChannel($channel_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE channel_id = $channel_id AND status = 1");
        $count = $result->fetch();
        $data['pay'] = $count[0];
        
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE channel_id = $channel_id AND status = 0");
        $count = $result->fetch();
        $data['nopay'] = $count[0];
        
        $result = $db->query("SELECT SUM(price) FROM ".PREFICS."order_items WHERE order_id IN ( SELECT order_id FROM ".PREFICS."orders WHERE channel_id = $channel_id AND status = 1) ");
        $summ = $result->fetch();
        $data['summ'] = $summ[0];
        
        return $data;
    }
    
    
    // ПОСЧИТАТЬ ЮЗЕРОВ ПО КАНАЛАМ
    public static function getUserStatByChannel($channel_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."users WHERE channel_id = $channel_id");
        $count = $result->fetch();
        return $count[0];
    }
    
    
    // ПОИСК КАНАЛА
    public static function searchChannel($utm)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT channel_id, hits FROM ".PREFICS."channels WHERE utm = '$utm' LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) {
            $hits = $data['hits'] + 1;
            $sql = 'UPDATE '.PREFICS.'channels SET hits = :hits WHERE channel_id = '.$data['channel_id'];
            $result = $db->prepare($sql);
            $result->bindParam(':hits', $hits, PDO::PARAM_INT);
            $result->execute();
            return $data['channel_id'];   
        }
        else return false;
    }
    
    
    // ДАННЫЕ КАНАЛА ПО ID 
    public static function getChannelData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."channels WHERE channel_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК КАНАЛОВ
    public static function getChannelList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."channels ORDER BY channel_id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['channel_id'] = $row['channel_id'];
            $data[$i]['group_id'] = $row['group_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['hits'] = $row['hits'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }   
    
     
    
    // СОЗДАТЬ КАНАЛ
    public static function addChannel($name, $group, $desc, $source, $medium, $campaign, $content, $term, $summ)
    {
        $db = Db::getConnection();
        $time = time();
        $utm1 = '?utm_source='.$source;
        if(!empty($medium)) $utm2 = '&utm_medium='.$medium;
        else $utm2 = '';
        
        if(!empty($campaign)) $utm3 = '&utm_campaign='.$campaign;
        else $utm3 = '';
        
        if(!empty($content)) $utm4 = '&utm_content='.$content;
        else $utm4 = '';
        
        if(!empty($term)) $utm5 = '&utm_term='.$term;
        else $utm5 = '';
        
        $utm = $utm1 . $utm2 . $utm3 . $utm4 . $utm5;
        
        
        $sql = 'INSERT INTO '.PREFICS.'channels (name, source, group_id, medium, campaign, content, term, summ, channel_desc, create_date, utm ) 
                VALUES (:name, :source, :group_id, :medium, :campaign, :content, :term, :summ, :channel_desc, :create_date, :utm)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':source', $source, PDO::PARAM_STR);
        $result->bindParam(':medium', $medium, PDO::PARAM_STR);
        $result->bindParam(':campaign', $campaign, PDO::PARAM_STR);
        $result->bindParam(':content', $content, PDO::PARAM_STR);
        $result->bindParam(':term', $term, PDO::PARAM_STR);
        $result->bindParam(':utm', $utm, PDO::PARAM_STR);
        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        $result->bindParam(':group_id', $group, PDO::PARAM_INT);
        $result->bindParam(':channel_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':create_date', $time, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ КАНАЛ
    public static function editChannel($id, $name, $group, $desc, $source, $medium, $campaign, $content, $term, $summ)
    {
        $db = Db::getConnection();  
        $utm1 = '?utm_source='.$source;
        if(!empty($medium)) $utm2 = '&utm_medium='.$medium;
        else $utm2 = '';
        
        if(!empty($campaign)) $utm3 = '&utm_campaign='.$campaign;
        else $utm3 = '';
        
        if(!empty($content)) $utm4 = '&utm_content='.$content;
        else $utm4 = '';
        
        if(!empty($term)) $utm5 = '&utm_term='.$term;
        else $utm5 = '';
        
        $utm = $utm1 . $utm2 . $utm3 . $utm4 . $utm5;
        
        $sql = 'UPDATE '.PREFICS.'channels SET name = :name, source = :source, medium = :medium, campaign = :campaign, content = :content, 
                term = :term, summ = :summ, channel_desc = :channel_desc, utm = :utm, group_id = :group_id WHERE channel_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':source', $source, PDO::PARAM_STR);
        $result->bindParam(':medium', $medium, PDO::PARAM_STR);
        $result->bindParam(':campaign', $campaign, PDO::PARAM_STR);
        $result->bindParam(':content', $content, PDO::PARAM_STR);
        $result->bindParam(':term', $term, PDO::PARAM_STR);
        $result->bindParam(':utm', $utm, PDO::PARAM_STR);
        $result->bindParam(':group_id', $group, PDO::PARAM_INT);
        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        $result->bindParam(':channel_desc', $desc, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    
    
    // СОЗДАТЬ ГРУППУ
    public static function addGroup($name)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'channels_group (name ) 
                VALUES (:name)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ ГРУППУ
    public static function editGroup($id, $name)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'channels_group SET name = :name WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ГРУПП
    public static function getGroupList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."channels_group ORDER BY id ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // ПОУЧИТЬ ДАННЫЕ ГРУППЫ
    public static function getGroupData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."channels_group WHERE id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // УДАЛИТЬ КАНАЛ
    public static function deleteChannel($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'channels WHERE channel_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ ГРУППУ
    public static function deleteGroup($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'channels_group WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }
}