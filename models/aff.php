<?php defined('BILLINGMASTER') or die;

class Aff {
    
	
	// ЗАПИСАТЬ POSTBACK для партнёра
    public static function writePostback($partner_id, $postback, $fb_pixel)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'aff_partner_data SET postbacks = :postbacks, fb_pixel = :fb_pixel WHERE user_id = '.$partner_id;
        $result = $db->prepare($sql);
        $result->bindParam(':postbacks', $postback, PDO::PARAM_STR);
        $result->bindParam(':fb_pixel', $fb_pixel, PDO::PARAM_STR);
        return $result->execute();
    }
	
	
    // ОСОБЫЙ РЕЖИМ ПАРТНЁРКИ
    public static function SpecAff($total_aff, $partner_id, $order, $product_id)
    {
        $user = User::getUserById($partner_id);
        
        if($user['spec_aff'] == 1){
            
            $aff_params = User::getProductsForSpecAff($partner_id); // данные спец. параметров для юзера
            
            if($aff_params){
                foreach($aff_params as $item){
                    
                    if($product_id == $item['product_id']){
                        
                        // Если продукт находится в списке
                        
                        // Проверить какой платёж по счёту
                        $check = self::checkPayCount($product_id, $partner_id, $order['client_email']);
                        
                        // Если с 1 платежа
                        if($item['type'] == 1){
                            
                            if(!$check){
                                $comiss = $total_aff / 100 * $item['comiss'];
                                return $comiss;
                            } else return 0;   
                        }
                        
                        
                        // Если начисления со 2-го платежа
                        if($item['type'] == 2){
                            
                            if($check){
                            
                                $comiss = $total_aff / 100 * $item['comiss'];
                                return $comiss;
                            
                            } else return 0;
                        }
                        
                        
                        // Если плавающая схема
                        if($item['type'] == 3){
                            
                            $lines = explode("\r\n",$item['float_scheme']);
                            $comiss = 0;
                            
                            if($check){
                                
                                $check = $check + 1;
                                foreach ($lines as $value) {
                                    $line = explode("=", $value);
                              	  
                                	if($check == $line[0]) {
                                	   
                                       $comiss = $total_aff / 100 * $line[1];
                                       return $comiss;
                                	}
                                }
                                
                            } else {
                                
                                $first = 1;
                                foreach ($lines as $value) {
                                    $line = explode("=", $value);
                              	  
                                	if($first == $line[0]) {
                                	   
                                       $procent = $line[1];
                                       $comiss = $total_aff / 100 * $procent;
                                       return $comiss;
                                	}
                                }
                            }   
                            
                            return $comiss;
                        }
                        
                        
                    }
                    
                }
            }
            
            return false;
            
        } else return false;
    }
	
    
    // ПОСЧИТАТЬ КОМИССИЮ ПАРТНЁРУ
    public static function getPartnerComiss($partner, $product, $item, $aff_params)
    {
        if ($partner && $partner['custom_comiss'] != 0) { // если есть партнёр и у него индивид. комиссия
            $comiss = round(($item['price'] / 100) * $partner['custom_comiss']);
        } elseif ($partner && $product['product_comiss'] > 0) { // Если комисиия указана у продукта
            $comiss = round(($item['price'] / 100) * $product['product_comiss']);
        } elseif($aff_params['params']['aff_1_level'] > 0) { // если есть партнёр и комисиия из настроек партнёрки
            $comiss = round(($item['price'] / 100) * $aff_params['params']['aff_1_level']);
        } else $comiss = false;
        
        return $comiss;
    }
	
	
	// СКОЛЬКО НУЖНО ВЫПЛАТИТЬ ПРЯМО СЕЙЧАС
    public static function realPayNow($partner_id, $days)
    {
        // Получить дату последней выплаты
        $db = Db::getConnection();
        $result = $db->query(" SELECT MAX(date) as max FROM ".PREFICS."aff_transaction WHERE user_id = $partner_id AND type = 0");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        if($data['max'] != null) $start = $data['max'];
        else $start = 1583377200; // 5 марта 2020 года
        
        $finish = time() - ($days * 86400);
        
        if($finish > $start) {
            
            $sql = " SELECT SUM(summ) FROM ".PREFICS."aff_transaction WHERE user_id = $partner_id AND date > $start AND date < $finish ";
            $result = $db->query($sql);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            if(!empty($data)) return $data['SUM(summ)'];
            else return false;   
            
        } else return false;
    }
	
    
    
    // ПРОВЕРКА ПЛАТЕЖА ПО СЧЁТУ
    public static function checkPayCount($product_id, $partner_id, $email)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."aff_transaction WHERE user_id = $partner_id AND client_email = '$email' AND product_id = $product_id");
        $count = $result->fetch();
        if($count[0] > 0) return $count[0];
        else return false;
    }
    
    // ПОЛУЧИТЬ СПИСОК ПРИВЛЕЧЁННЫХ ЛЮДЕЙ
    public static function getUserFromPartner($userId)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."users WHERE from_id = $userId ORDER BY user_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['user_name'] = $row['user_name'];
            $data[$i]['email'] = $row['email'];
            $data[$i]['reg_date'] = $row['reg_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК ПАРТНЁРОВ, КОМУ НУЖНО СДЕЛАТЬ ВЫПЛАТУ 
    public static function getPartnersToPay($all = null)
    {
        $db = Db::getConnection();
        if($all == null)$result = $db->query("SELECT DISTINCT user_id, SUM(summ), SUM(pay) FROM ".PREFICS."aff_transaction GROUP BY user_id HAVING SUM(summ) > SUM(pay) ");
        else $result = $db->query("SELECT DISTINCT user_id, SUM(summ), SUM(pay) FROM ".PREFICS."aff_transaction GROUP BY user_id ");
        $i = 0;
        if($result){
            while($row = $result->fetch()){
            $data[$i]['user_id'] = $row['user_id'];
            $data[$i]['summ'] = $row['SUM(summ)'];
            $data[$i]['pay'] = $row['SUM(pay)'];
            $i++;
            }
            if(isset($data)) return $data;   
        }
        else return false;
    }
    
    
    // ОБНОВИТЬ КОМИССИЮ
    public static function reloadComiss($id, $summ, $type)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.$type.'_transaction SET summ = :summ WHERE id = '.$id;
        
        $result = $db->prepare($sql);
        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ СПИСОК АВТОРОВ, КОМУ НУЖНО ВЫПЛАТИТЬ
    public static function getAuthorsToPay($all = null)
    {
        $db = Db::getConnection();
        if($all == null) $result = $db->query("SELECT DISTINCT user_id, SUM(summ), SUM(pay) FROM ".PREFICS."author_transaction GROUP BY user_id HAVING SUM(summ) > SUM(pay) ");
        else $result = $db->query("SELECT DISTINCT user_id, SUM(summ), SUM(pay) FROM ".PREFICS."author_transaction GROUP BY user_id ");
        $i = 0;
        if($result){
            while($row = $result->fetch()){
                $data[$i]['user_id'] = $row['user_id'];
                $data[$i]['summ'] = $row['SUM(summ)'];
                $data[$i]['pay'] = $row['SUM(pay)'];
                $i++;
            }
            if(isset($data)) return $data;
        }
        else return false;
    }
    
    
    // ТОП ПАРТНЁРОВ
    public static function getTopPartners()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT DISTINCT user_id, SUM(summ) FROM ".PREFICS."aff_transaction GROUP BY user_id HAVING SUM(summ) > 0 ORDER BY SUM(summ) DESC");
        $i = 0;
        if($result){
            while($row = $result->fetch()){
                $data[$i]['user_id'] = $row['user_id'];
                $data[$i]['summ'] = $row['SUM(summ)'];
                $i++;
            }
            if(isset($data)) return $data;
        }
        else return false;
    }
    
    
    // КОЛИЧЕСТВО ЗАКАЗОВ ПАРТНЁРА 
    public static function CountOrdersToPartner($id, $status = 1, $summ = null)
    {
        $db = Db::getConnection();
        if($summ != null) $summ = ' AND summ > 0';
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."orders WHERE status = $status AND partner_id = $id $summ");
        $count = $result->fetch();
        if(isset($count))return $count[0];
        else return false;
    }
    
    
    // СПИСОК ЗАКАЗОВ ПАРТНЁРА
    // ПРИНИМАЕТ ID ПАРТНЁРА И СТАТУС, если all - значит статус не участвует в выборке
    public static function getPartnersOrders($id, $status, $paid = null)
    {

        $db = Db::getConnection();
        if($status == 'all'){
            $where =  $where2 = $id;
        } else {
            $where = $id.' AND oi.price > 0';
            $where2 = $id.' AND o.summ > 0';
        }
        $result = $db->query("SELECT o.order_date, o.payment_date, oi.product_id, o.client_email, 
                                    o.status, o.order_id, oi.price as summ, o.partner_id,
                                    aff.summ as trans_summ FROM ".PREFICS."aff_transaction as aff 
                                    LEFT JOIN ".PREFICS."orders as o ON o.order_id = aff.order_id
                                    LEFT JOIN ".PREFICS."order_items as oi ON aff.order_id = oi.order_id  AND aff.product_id = oi.product_id
                                    WHERE aff.user_id = $where AND aff.order_id <> 0
                                UNION
                                    SELECT o.order_date, o.payment_date, oi.product_id, o.client_email, o.status, o.order_id, oi.price as summ, o.partner_id,
                                    aff.summ as trans_summ FROM ".PREFICS."aff_transaction as aff RIGHT JOIN 
                                    ".PREFICS."orders as o ON o.order_id = aff.order_id 
                                    RIGHT JOIN ".PREFICS."order_items as oi ON aff.order_id = oi.order_id  AND aff.product_id = oi.product_id
                                    WHERE aff.user_id = $where AND aff.order_id is null
                                UNION
                                    SELECT o.order_date, o.payment_date, o.product_id, o.client_email, o.status, o.order_id, o.summ as summ, o.partner_id,
                                    null as trans_summ FROM ".PREFICS."orders as o LEFT JOIN ".PREFICS."aff_transaction as afft ON o.order_id = 
                                    afft.order_id 
                                    WHERE o.partner_id = $where2 AND afft.order_id is null ORDER BY order_date DESC");
        $i = 0;
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // СУММА ВОЗНАРАЖДЕНИЯ ДЛЯ ПАРТНЁРА В ЗАКАЗЕ
    public static function getSummTransactionByOrder($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(summ) FROM ".PREFICS."aff_transaction WHERE order_id = $id");
        $count = $result->fetch();
        return $count['SUM(summ)'];
    }
    
    // ТО ЖЕ САМОЕ что и выше, СУММА ВОЗНАГРАЖДЕНИЯ, только берётся не по ID заказа, а по ID Записи.
    public static function getSummTransactionByTransactID($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(summ) FROM ".PREFICS."aff_transaction WHERE id = $id");
        $count = $result->fetch();
        return $count['SUM(summ)'];
    }
    
    
    
    // ДОБАВИТЬ КОРОТКУЮ ССЫЛКУ
    public static function AddPartnerShortLink($userId, $url, $desc)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'aff_short_links (partner_id, url, link_desc ) 
                VALUES (:partner_id, :url, :link_desc)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':partner_id', $userId, PDO::PARAM_INT);
        $result->bindParam(':url', $url, PDO::PARAM_STR);
        $result->bindParam(':link_desc', $desc, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ КОРОТКУЮ ССЫЛКУ 
    public static function deleteShortLink($link_id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'aff_short_links WHERE link_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $link_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ КОРОТКИЕ ССЫЛКИ ПАРТНЁРА
    public static function getShortLinkByPartner($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."aff_short_links WHERE partner_id = $id ORDER BY date DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['url'] = $row['url'];
            $data[$i]['link_desc'] = $row['link_desc'];
            $data[$i]['link_id'] = $row['link_id'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // КОЛ-ВО ПЕРЕХОДОВ ПО ССЫЛКАМ ПАРТНЁРА
    public static function contHitsToPartner($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT hits FROM ".PREFICS."aff_partner_data WHERE user_id = $id");
        $count = $result->fetch();
        if($count)return $count['hits'];
        else return false;
    }
    
    
    
    // СПИСОК АВТОРСКИХ НАЧИСЛЕНИЙ
    public static function getAuthorTransaction($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."author_transaction WHERE type = 1 AND user_id = $user_id ORDER BY date DESC LIMIT 50");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['order_id'] = $row['order_id'];
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['date'] = $row['date'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ДАННЫЕ ПО НАЧИСЛЕНИЯМ АВТОРА ИЛИ ПАРТНЁРА
    public static function getUserTransactData($user_id, $role, $date = null)
    {
        $db = Db::getConnection();
        if($date == null) $result = $db->query("SELECT SUM(summ), SUM(pay) FROM ".PREFICS.$role."_transaction WHERE user_id = $user_id ");
        else $result = $db->query("SELECT SUM(summ), SUM(pay) FROM ".PREFICS.$role."_transaction WHERE user_id = $user_id AND date < $date");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;				  
    }
    
    
    // ДАННЫЕ ПО ВЫПЛАТАМ ПАРНЁРА
    public static function getParnerLastPay($user_id, $role)
    {
        $now = time();
        $db = Db::getConnection();
        $result = $db->query("SELECT pay, date FROM ".PREFICS.$role."_transaction WHERE user_id = $user_id AND type = 0 ORDER BY date DESC LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
	
	// СУММА ЗА ПОСЛЕДНИЙ МЕСЯЦ
    public static function getLastMonthPay($user_id, $role, $start, $end)
    {  
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(summ), SUM(pay) FROM ".PREFICS.$role."_transaction WHERE user_id = $user_id AND date > $start AND date < $end");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ПОЛУЧИТЬ ИСТОРИЮ ВЫПЛАТ                                     
    public static function getHistoryTransaction($user_id, $type, $role, $summ = null)
    {
        $db = Db::getConnection();

        $query = "SELECT tr.*, o.client_email, o.order_date FROM ".PREFICS.$role."_transaction AS tr
            LEFT JOIN ".PREFICS."orders AS o
            ON tr.order_id = o.order_id
            WHERE tr.user_id = $user_id
            AND tr.type = $type".
            ($summ != null ? ' AND tr.summ > 0' : '')."
            GROUP BY tr.id 
            ORDER BY tr.date DESC";

        $result = $db->query($query);

        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['order_id'] = $row['order_id'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['pay'] = $row['pay'];
            $data[$i]['type'] = $row['type'];
            $data[$i]['date'] = $row['date'];
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['client_email'] = $row['client_email'];
            $data[$i++]['order_date'] = $row['order_date'];
        }

        return isset($data) && !empty($data) ? $data : false;
    }
    
    
    
    
    // РАСЧЁТ ПАРТНЁРСКИХ ВОЗНАГРАЖДЕНИЙ
    public static function PartnerComissCalc($order, $product_id, $total_aff, $aff_params)
    {
        if($total_aff != 0){
            $aff_transact = self::PartnerTransaction($order['partner_id'], $order['order_id'], round($total_aff), 0, 1);
            if($aff_transact){
                $send = self::SendPartnerTransaction($order['partner_id'], $order['order_date'], round($total_aff), 0);
            }
            
            // Партнёрка 2-ур 
            if($aff_params['params']['aff_2_level'] != 0){
                
                $data = self::getPartnerReq($order['partner_id']);
                if($data['ref_id'] != 0){
                    
                    // Расчитать комиссию 2-го уровня
                    $total_aff2 = ($total_aff / $aff_params['params']['aff_1_level']) * $aff_params['params']['aff_2_level'];
                    $aff_transact = self::PartnerTransaction($data['ref_id'], $order['order_id'], round($total_aff2), 0, 1);
                    $send2 = self::SendPartnerTransaction($data['ref_id'], $order['order_date'], round($total_aff2), 1);
                    
                    
                    // Партнёрка 3-ий уровень 
                    if($aff_params['params']['aff_3_level'] != 0){
                        
                        // Расчитать комиссию 3-го уровня
                        $total_aff3 = ($total_aff / $aff_params['params']['aff_1_level']) * $aff_params['params']['aff_3_level'];
                        $data2 = self::getPartnerReq($data['ref_id']);
                        
                        if($data2['ref_id'] != 0){
                            $aff_transact = self::PartnerTransaction($data2['ref_id'], $order['order_id'], round($total_aff3), 0, 1);
                            $send3 = self::SendPartnerTransaction($data2['ref_id'], $order['order_date'], round($total_aff3), 1);
                        }
                        
                    }
                    
                }
            }
        }
    }
    
    
    
    // РАСЧЧЁТ АВТОРСКИХ ВОЗНАГРАЖДЕНИЙ
    public static function AuthorComissCalc($order, $product, $item, $total_after)
    {
            // Если есть 1-ый автор
            if($product['author1'] != null){
                if($product['type_comiss1'] == 'summ'){
                    $author_1 = $product['comiss1']; // начисления автору в рублях
                } else {
                    $author_1 = ($total_after / 100) * $product['comiss1']; // начисление автору процентов с остатка
                }
                
                // Начислить 1 автору 
                $transact = self::AuthorTransaction($product['author1'], $order['order_id'], $item['product_id'], round($author_1), 0, 1);
                
                // Отправить письмо 1 автору
                if($transact) $send = self::SendAuthorTransaction($product['author1'], $item['product_id'], round($author_1));
            }
            
                // 2-ой автор
            if($product['author2'] != null){
                if($product['type_comiss2'] == 'summ'){
                    $author_2 = $product['comiss2'];
                } else {
                    $author_2 = ($total_after / 100) * $product['comiss2'];
                }
                
                // Начислить 2 автору 
                $transact = self::AuthorTransaction($product['author2'], $order['order_id'], $item['product_id'], round($author_2), 0, 1);
                // Отправит письмо 2 автору
                if($transact) $send = self::SendAuthorTransaction($product['author2'], $item['product_id'], round($author_2));
            }
                
            // 3-ий автор
            if($product['author3'] != null){
                if($product['type_comiss3'] == 'summ'){
                    $author_3 = $product['comiss3'];
                } else {
                    $author_3 = ($total_after / 100) * $product['comiss3'];
                }
                
                // Начислить 3 автору 
                $transact = self::AuthorTransaction($product['author3'], $order['order_id'], $item['product_id'], round($author_3), 0, 1);
                // Отправит письмо 3 автору
                if($transact) $send = self::SendAuthorTransaction($product['author3'], $item['product_id'], round($author_3));
            }
            
            if($transact) return true;
            return false;
            
    }
    
    
    // ЗАПИСЬ АВТОРСКИХ КОМИССИОННЫХ
    public static function AuthorTransaction($user_id, $order_id, $product_id, $summ, $pay, $type)
    {
        $date = time();
        $db = Db::getConnection();
        
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."author_transaction WHERE product_id = $product_id AND type = $type AND date = $date AND user_id = $user_id");
        $count = $result->fetch();
        if($count[0] == 0) {
            
            $sql = 'INSERT INTO '.PREFICS.'author_transaction (user_id, order_id, product_id, summ, pay, type, date ) 
                VALUES (:user_id, :order_id, :product_id, :summ, :pay, :type, :date)';
            
            $result = $db->prepare($sql);
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $result->bindParam(':summ', $summ, PDO::PARAM_INT);
            $result->bindParam(':pay', $pay, PDO::PARAM_INT);
            $result->bindParam(':type', $type, PDO::PARAM_INT);
            $result->bindParam(':date', $date, PDO::PARAM_INT);
            return $result->execute();
               
        } else return false;
    }
    
    
    // ЗАПИСЬ ПАРТНЁРСКОЙ КОМИССИИ
    public static function PartnerTransaction($user_id, $order_id, $product_id, $summ, $pay, $type, $email = null)
    {
        $date = time();
		if(empty($user_id)) return false;
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'aff_transaction (user_id, order_id, product_id, summ, pay, type, date, client_email ) 
                VALUES (:user_id, :order_id, :product_id, :summ, :pay, :type, :date, :client_email)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':summ', $summ, PDO::PARAM_INT);
        $result->bindParam(':pay', $pay, PDO::PARAM_INT);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        $result->bindParam(':date', $date, PDO::PARAM_INT);
        $result->bindParam(':client_email', $email, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    
    // ПОЛУЧИТЬ ПАРТНЁРСКИЕ ССЫЛКИ для партнёров
    public static function getPartnerLinks()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."products WHERE in_partner = 1 AND status = 1 ORDER BY product_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['product_name'] = $row['product_name'];
            $data[$i]['product_alias'] = $row['product_alias'];
            $data[$i]['product_title'] = $row['product_title'];
            $data[$i]['price'] = $row['price'];
            $data[$i]['red_price'] = $row['red_price'];
            $data[$i]['ads'] = $row['ads'];
            $data[$i]['external_landing'] = $row['external_landing'];
			$data[$i]['external_url'] = $row['external_url'];
            $data[$i]['run_aff'] = $row['run_aff'];
            $data[$i]['product_comiss'] = $row['product_comiss'];
            $i++;
        }
        if(isset($data)) return $data;
        else return false;
    }
    
    
    
    // СОЗДАТЬ НОВОГО ПАРТНЁРА
    public static function AddNewPartner($name, $email, $pass, $about, $param, $date, $reg_key, $partner_group, $partner_id)
    {
        $param = explode(";", $param);
        $is_partner = 1;
        $role = 'user';
        $status = 0;
        $enter_method = 'affreg';
        
        $db = Db::getConnection();
        
        // Проверить существование юзера по емейлу
        $result = $db->query("SELECT user_id FROM ".PREFICS."users WHERE email = '$email'");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        if(isset($data) && !empty($data)){
            return false;
        }
        
        $sql = 'INSERT INTO '.PREFICS.'users (user_name, email, pass, note, is_partner, role, enter_time, reg_date, enter_method, reg_key, status, from_id ) 
                VALUES (:name, :email, :pass, :note, :is_partner, :role, :enter_time, :reg_date, :enter_method, :reg_key, :status, :from_id)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':pass', $pass, PDO::PARAM_STR);
        $result->bindParam(':note', $about, PDO::PARAM_STR);
        $result->bindParam(':is_partner', $is_partner, PDO::PARAM_INT);
        $result->bindParam(':role', $role, PDO::PARAM_STR);
        $result->bindParam(':reg_date', $date, PDO::PARAM_INT);
        $result->bindParam(':enter_time', $param[0], PDO::PARAM_INT);
        $result->bindParam(':reg_key', $reg_key, PDO::PARAM_STR);
        $result->bindParam(':enter_method', $enter_method, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':from_id', $partner_id, PDO::PARAM_INT);
        $result->execute();
        
        // Получить ID только что созданного партнёра
        $result = $db->query(" SELECT user_id FROM ".PREFICS."users WHERE email = '$email' ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($data)) {
            // Записать строку для учёта статистики переходов
            $str = self::AddStatRow($data['user_id'], $partner_id);
            
            // Записать группу для партнёра
            if ($partner_group != null) {
                $write = User::WriteUserGroup($data['user_id'], $partner_group);
            }
            
            return $str;
        } else {
            exit('Ошибка при регистрации партнёра aff:69');
        }
    }
    
    
    // ДОБАВЛЯЕМ ЗАРЕГИСТРИРОВАННОГО ЮЗЕРА В ПАРТНЁРЫ
    public static function AddUserToPartner($user_id, $partner_id)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'users SET is_partner = 1 WHERE user_id = :user_id';
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result->execute();
        
        $str = self::AddStatRow($user_id, $partner_id);
        return $str;
    }
    
    
    
    // ДОБАВИТЬ СТРОКУ ДЛЯ ДАННЫХ НОВОГО ПАРТНЁРА
    public static function AddStatRow ($user_id, $partner_id) 
    {
        $db = Db::getConnection();
        
        // Проверить на существование
        $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."aff_partner_data WHERE user_id = $user_id");
        $count = $result->fetch();
        if($count[0] == 0){
            $hits = 0;
            $sql = 'INSERT INTO '.PREFICS.'aff_partner_data (user_id, hits, ref_id ) 
                    VALUES (:user_id, :hits, :ref_id)';
            
            $result = $db->prepare($sql);
            $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $result->bindParam(':hits', $hits, PDO::PARAM_INT);
            $result->bindParam(':ref_id', $partner_id, PDO::PARAM_INT);
            return $result->execute(); 
        }  
        return true;
    }
    
    
    
    // ДОБАВЛЯЕМ / СОХРАНЯЕМ РЕКВИЗИТЫ ПАРТНЁРА
    public static function UpdateReq($user_id, $req)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'aff_partner_data SET requsits = :requsits WHERE user_id = :user_id';
        $result = $db->prepare($sql);
        $result->bindParam(':requsits', $req, PDO::PARAM_STR);
        $result->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ ПАРТНЁРА
    public static function getPartnerReq($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."aff_partner_data WHERE user_id = $user_id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ИЗМЕНИТЬ ИНДИВИДУАЛЬНУЮ КОМИССИЮ ПАРТНЁРУ
    public static function updateCustomComiss($user_id, $custom_comiss)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'aff_partner_data SET custom_comiss = :custom_comiss WHERE user_id = '.$user_id;
        $result = $db->prepare($sql);
        $result->bindParam(':custom_comiss', $custom_comiss, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // СДЕЛАТЬ (УДАЛИТЬ) ЮЗЕРА АВТОРОМ
    public static function AuthorAction($id, $value)
    {
        $db = Db::getConnection();  
        $hits = 0;
        $ref_id = 0;
        
        $sql = 'UPDATE '.PREFICS.'users SET is_author = :value WHERE user_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':value', $value, PDO::PARAM_INT);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();
        
        if($value == 1){
        
            $result = $db->query("SELECT COUNT(user_id) FROM ".PREFICS."aff_partner_data WHERE user_id = $id");
            $count = $result->fetch();
            if($count[0] == 0){

                $sql = 'INSERT INTO '.PREFICS.'aff_partner_data (user_id, hits, ref_id ) 
                        VALUES (:user_id, :hits, :ref_id)';
                
                $result = $db->prepare($sql);
                $result->bindParam(':user_id', $id, PDO::PARAM_INT);
                $result->bindParam(':hits', $hits, PDO::PARAM_INT);
                $result->bindParam(':ref_id', $ref_id, PDO::PARAM_INT);
                return $result->execute();
            }
        }
    }


    // Получить кол-во месяцев по которым есть данные в таблицах aff_transaction и orders
    // по партнеру
    public static function CountMonthHasDate($userId)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT period_diff(date_format(now(), '%Y%m'), date_format(min(FROM_UNIXTIME(order_date)), '%Y%m')) as months 
                            from ".PREFICS."orders WHERE partner_id = $userId
                            UNION
                            SELECT period_diff(date_format(now(), '%Y%m'), date_format(min(FROM_UNIXTIME(date)), '%Y%m')) as months 
                            from ".PREFICS."aff_transaction WHERE user_id = $userId
                            ORDER BY months DESC");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data['months'];
        else return false;
    }
    
    // Получить данные для таблицы по кликам и статистке 
    public static function getDateForMainTable($userId)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT t1.period, sum(t1.kol) as invoice, sum(t1.kol_pay) as pay_invoice, sum(t1.payments) as payments,
                            sum(t1.hits) as hits  FROM (SELECT 
                            date_format(FROM_UNIXTIME(order_date), '%Y%m') as period, count(order_date) as 
                            kol, 0 as kol_pay, 0 as payments, 0 as hits
                            FROM ".PREFICS."orders
                            WHERE partner_id = $userId and summ > 0 and status <> 9
                            GROUP BY period
                            UNION
                            SELECT 
                            date_format(FROM_UNIXTIME(payment_date), '%Y%m') as period, 0 as 
                            kol, count(payment_date) as kol_pay,0 as payments, 0 as hits
                            FROM ".PREFICS."orders
                            WHERE partner_id = $userId and summ > 0 and status <> 9
                            GROUP BY period
                            UNION
                            SELECT 
                            date_format(FROM_UNIXTIME(date), '%Y%m') as period, 0, 0, sum(summ), 0 as hits
                            FROM ".PREFICS."aff_transaction
                            WHERE user_id = $userId and summ > 0
                            GROUP BY period
                            UNION
                            SELECT date_format(date, '%Y%m') as period, 0, 0, 0, hits as hits
                            FROM ".PREFICS."aff_stat_hits
                            WHERE partner_id = $userId) as t1
                            group by t1.period ORDER BY period DESC");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    


    
    // Изменить настройки
    public static function SaveAffSetting($params, $status)
    {
        $db = Db::getConnection();  
        $sql = "UPDATE ".PREFICS."extensions SET params = :params, enable = :enable WHERE name = 'partnership'";
        $result = $db->prepare($sql);
        $result->bindParam(':params', $params, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПРОВЕРИТЬ СУЩЕСТВОВАНИЕ ПАРТНЁРА
    public static function PartnerVerify($user_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT user_id, email FROM ".PREFICS."users WHERE user_id = $user_id AND is_partner = 1 AND status = 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ПРОВЕРИТЬ СУЩЕСТВОВАНИЕ ПАРТНЁРА И ЗАПИСАТЬ ЕМУ ПЕРЕХОД ПО ЕГО ССЫЛКЕ
    public static function AffHits($user_id)
    {
        $cur_date = date('Y-m-d');
        $db = Db::getConnection();
        $result = $db->query(" SELECT user_id FROM ".PREFICS."users WHERE user_id = $user_id AND is_partner = 1 AND status = 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) {
            
            $result = $db->query(" SELECT hits FROM ".PREFICS."aff_partner_data WHERE user_id = $user_id ");
            $data = $result->fetch(PDO::FETCH_ASSOC);
            if(isset($data)){
                $count = $data['hits'] + 1;
                
                $sql = 'UPDATE '.PREFICS.'aff_partner_data SET hits = :hits WHERE user_id = :id';
                $result = $db->prepare($sql);
                $result->bindParam(':hits', $count, PDO::PARAM_INT);
                $result->bindParam(':id', $user_id, PDO::PARAM_INT);
                $result->execute();
             
                $hits = 1;
                $sql2 = "INSERT INTO ".PREFICS."aff_stat_hits (partner_id, date, hits)
                VALUES(:partner_id, :date, :hits) ON DUPLICATE KEY UPDATE hits= hits + 1";

                $result2 = $db->prepare($sql2);
                $result2->bindParam(':partner_id', $user_id, PDO::PARAM_INT);
                $result2->bindParam(':date', $cur_date, PDO::PARAM_STR);
                $result2->bindParam(':hits', $hits, PDO::PARAM_INT);
                return $result2->execute();

            }
            
        } else {
            return false;
        }
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ РЕДИРЕКТА ДЛЯ ПАРТНЁРА
    public static function getAffRedirect($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."aff_short_links WHERE link_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data)) return $data;
        else return false;
    }
    
    
    // ПОДСЧЁТ СУММЫ ЗАРАБОТАННОЙ ВСЕМИ ПАРТНЁРАМИ
    public static function getPartnerSummTotal()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT SUM(summ) FROM ".PREFICS."aff_transaction");
        $count = $result->fetch();
        if($count)return $count[0];
        else return false;
    }
    
    
    
    // УДАЛИТЬ ВЫПЛАТУ АВТОРА
    public static function deleteAuthorTransaction($order_id, $product_id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'author_transaction WHERE order_id = :order_id AND product_id = :product_id';
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // УДАЛИТЬ ВЫПЛАТУ ПАРТНЁРА
    public static function deletePartnerTransaction($order_id, $product_id = null)
    {
        $db = Db::getConnection();
        if ($product_id == null) {
            $sql = 'UPDATE '.PREFICS.'aff_transaction SET summ = 0 WHERE order_id = :order_id';    
        } else {
            $sql = 'UPDATE '.PREFICS.'aff_transaction SET summ = 0 WHERE order_id = :order_id AND product_id = :product_id';    
        }
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        if ($product_id != null) {
            $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        }
        return $result->execute();
    }

    // УДАЛИТЬ ПАРТНЁРА ИЗ ЗАКАЗА
    public static function deletePartnerFromOrder($order_id)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'orders SET partner_id = null WHERE order_id = :order_id';             
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    /**
     *  EMAIL 
     */
    
        // УВЕДОМЛЕНИЕ ПАРТНЁРА / АВТОРА О ВЫПЛАТЕ
    public static function SendPartnerNotifOfPay($user_id, $summ)
    {
        // Получаем настройки
        $setting = System::getSetting();
        
        // Получить емейл партнёра
        $user = User::getUserById($user_id);
        $email = $user['email'];
        
        // Реплейсим письмо
        $replace = array(
        '[NAME]' => $user['user_name'],
        '[SUMM]' => $summ,
        '[CURRENCY]' => $setting['currency']
        );
        
        $letter = System::Lang('PARTNER_NOTIF_TRANSACT_MESS');
        $text = strtr($letter, $replace);
        $subject = System::Lang('PARTNER_NOTIF_TRANSACT_SUBJ');
        $from = $setting['sender_email'];
        $from_name = $setting['sender_name'];
        
        if($setting['use_smtp'] == 1){
            
            // Отправляем через SMTP
            $send = Email::SMTPSingleSender($email, $subject, $text, $setting);
            
        } else {
            
            // Отправляем через Mail()
            $headers= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html;charset=utf-8 \r\n";
            $headers .= "From: $from_name <$from>\r\n";
            $headers .= "Reply-To: $from \r\n";
            
            $send = mail($email, $subject, $text, $headers );
        }
    }
    
    
    
        // УВЕДОМЛЕНИЕ ПАРТНЁРА О ЗАКАЗЕ
    public static function SendPartnerTransaction($user_id, $order, $summ, $level)
    {
        // Получаем настройки
        $setting = System::getSetting();
        
        // Получить емейл партнёра
        $user = User::getUserById($user_id);
        $email = $user['email'];
        
        // Реплейсим письмо
        $replace = array(
        '[NAME]' => $user['user_name'],
        '[ORDER]' => $order,
        '[SUMM]' => $summ,
        '[CURRENCY]' => $setting['currency']
        );
        
        if($level == 0) $letter = System::Lang('PARTNER_TRANSACT_MESS');
        else $letter = System::Lang('PARTNER_TRANSACT_MESS_LEVEL');
        $text = strtr($letter, $replace);
        $subject = System::Lang('PARTNER_TRANSACT_SUBJ');
        $from = $setting['sender_email'];
        $from_name = $setting['sender_name'];
        
        if($setting['use_smtp'] == 1){
            
            // Отправляем через SMTP
            $send = Email::SMTPSingleSender($email, $subject, $text, $setting);
            
        } else {
            
            // Отправляем через Mail()
            $headers= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html;charset=utf-8 \r\n";
            $headers .= "From: $from_name <$from>\r\n";
            $headers .= "Reply-To: $from \r\n";
            
            $send = mail($email, $subject, $text, $headers );
        }
    }
    
    
        // УВЕДОМЛЕНИЕ АВТОРУ О ПРОДАЖЕ
    public static function SendAuthorTransaction($user_id, $product_id, $summ)
    {
        // Получаем настройки
        $setting = System::getSetting();
        
        // Получить емейл автора
        $user = User::getUserById($user_id);
        $email = $user['email'];
        $product = Product::getProductName($product_id);
        
        // Реплейсим письмо
        $replace = array(
        '[NAME]' => $user['user_name'],
        '[PRODUCT]' => $product['product_name'],
        '[SUMM]' => $summ,
        '[CURRENCY]' => $setting['currency']
        );
        
        $letter = System::Lang('AUTHOR_TRANSACT_MESS');
        $text = strtr($letter, $replace);
        $subject = System::Lang('AUTHOR_TRANSACT_SUBJ');
        $from = $setting['sender_email'];
        $from_name = $setting['sender_name'];
        
        if($setting['use_smtp'] == 1){
            
            // Отправляем через SMTP
            $send = Email::SMTPSingleSender($email, $subject, $text, $setting);
            
        } else {
            
            // Отправляем через Mail()
            $headers= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html;charset=utf-8 \r\n";
            $headers .= "From: $from_name <$from>\r\n";
            $headers .= "Reply-To: $from \r\n";
            
            $send = mail($email, $subject, $text, $headers );
        }
        
    }
    
    
    public static function getPartnerGroup() {
        $aff_set = unserialize(System::getExtensionSetting('partnership'));
        $aff_set['params']['partner_group'];
        
        return !empty($aff_set['params']['partner_group']) ? $aff_set['params']['partner_group'] : null;
    }
}