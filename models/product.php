<?php defined('BILLINGMASTER') or die;

class Product {

    /**
     * @param array $exceptions
     * @return array
     */
    public static function getFields($exceptions = []) {
        $fields = [
            'integer' => [
                'product_id', 'installment', 'installment_addgroups', 'external_landing', 'send_pass', 'hidden_price',
                'product_comiss', 'price_layout', 'complect_sort', 'show_reviews', 'sell_once', 'run_aff',
                'product_amt', 'show_amt', 'cat_id', 'text1_tmpl', 'text2_tmpl', 'price', 'red_price',
                'in_catalog', 'in_partner', 'author1', 'author2', 'author3', 'comiss1', 'comiss2', 'comiss3',
                'subscription_id', 'base_id', 'status', 'upsell_1', 'upsell_2', 'upsell_3', 'upsell_1_price',
                'upsell_2_price', 'upsell_3_price', 'text1_heading', 'text2_heading', 'show_price_box', 'to_resale',
                'hits_1', 'hits_2',
            ],
            'string' => [
                'product_name', 'service_name', 'product_title', 'product_alias', 'product_cover', 'img_alt',
                'acymailing', 'note', 'manager_letter', 'installment_action', 'external_url', 'redirect_after',
                'type_id', 'button_text', 'custom_code', 'auto_add', 'complect_params', 'link', 'letter',
                'letter_subject', 'product_desc', 'product_text1', 'product_text2', 'text1_head', 'text2_head',
                'text1_bottom', 'text2_bottom', 'price_minmax', 'group_id', 'del_group_id', 'delivery_sub',
                'delivery_unsub', 'notif_url', 'type_comiss1', 'type_comiss2', 'type_comiss3', 'meta_desc',
                'meta_keys', 'pincodes', 'upsell_1_desc', 'upsell_2_desc', 'upsell_3_desc', 'upsell_1_text',
                'upsell_2_text', 'upsell_3_text', 'ads', 'code_price_box', 'select_payments',
            ],
        ];

        return [
            'integer' => array_diff($fields['integer'], $exceptions),
            'string' => array_diff($fields['string'], $exceptions),
        ];
    }


    public static function addWhere($where, $add, $and = true) 
    {
        if ($where){
            
          if ($and) $where .= " AND $add";
          else $where .= " OR $add";
          
        } else $where = $add;
        return $where;
    }
    
    
    // ПОЛУЧИТЬ ДЕЙСТВИЯ ПОСЛЕ ПЛАТЕЖЕЙ по РАССРОЧКЕ
    public static function getGroupAfterInstallPay($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."product_install_act WHERE product_id = $id ");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['actions'] = $row['actions'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    } 
    
    
    // ЗАПИСАТЬ ДЕЙСТВИЯ ПОСЛЕ ПЛАТЕЖЕЙ
    public static function writeGroupAfterInstallPay($product_id, $group_after_install)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."product_install_act WHERE product_id = $product_id ");
        $count = $result->fetch();
        if($count[0] > 0) {
            
            // обновляем 
            $sql = 'UPDATE '.PREFICS.'product_install_act SET actions = :actions WHERE product_id = '.$product_id;
            $result = $db->prepare($sql);
            $result->bindParam(':actions', $group_after_install, PDO::PARAM_STR);
            return $result->execute();
            
        } else {
            
            // записываем
            $sql = 'INSERT INTO '.PREFICS.'product_install_act (product_id, actions ) 
                    VALUES (:product_id, :actions)';
            
            $result = $db->prepare($sql);
            $result->bindParam(':actions', $group_after_install, PDO::PARAM_STR);
      		$result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            return $result->execute();
        }
    }


    /**
     * ПОЛУЧИТЬ СПИСОК РАССРОЧЕК
     * @param null $enable
     * @return array|bool
     */
    public static function getInstalments($enable = null)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."installment_tune";
        $query .= ($enable != null ? ' WHERE enable = 1' : '') . ' ORDER BY sort ASC';
        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return $data ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ РАССРОЧКИ
    public static function getInstallmentData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."installment_tune WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // УДАЛИТЬ РАССРОЧКУ
    public static function delInstallment($id)
    {
        $db = Db::getConnection();
        
        // Проверка на действующие рассрчоки
        $result = $db->query("SELECT * FROM ".PREFICS."installment_map WHERE installment_id = $id AND status = 1");
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row;
        }
        
        if(empty($data)){
            
            $sql = 'DELETE FROM '.PREFICS.'installment_tune WHERE id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();
        } else return false;
    }
	
	
	// ПОЛУЧИТЬ РАССРОЧКИ С ОПЛАТОЙ В ТЕКУЩЕМ МЕСЯЦЕ
    public static function getSummFromInstallmentCurrMonth($start, $end)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."installment_map WHERE status = 1 AND next_pay > $start AND next_pay < $end ");
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
        	$data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }
    
    
    // ПОЛУЧИТЬ СПИСОК РАССРОЧЕК В КАРТЕ
    public static function getInstalmentsMap()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."installment_map ORDER BY next_pay DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['order_id'] = $row['order_id'];
            $data[$i]['installment_id'] = $row['installment_id'];
            $data[$i]['summ'] = $row['summ'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['max_periods'] = $row['max_periods'];
            $data[$i]['email'] = $row['email'];
            $data[$i]['comment'] = $row['comment'];
			$data[$i]['next_pay'] = $row['next_pay'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
	 // ПОДСЧИТАТЬ ЗАКАЗЫ ПО АКЦИИ
    public static function getCountAndSumToOrdersSale($sale_id)
    {
        $db = Db::getConnection();
        $query = "SELECT COUNT(DISTINCT o.order_id) AS count, SUM(ot.price) AS summ
                  FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS ot ON o.order_id = ot.order_id
                  WHERE o.sale_id = $sale_id AND o.status = 1";
        
        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return $data['count'] > 0 ? $data : false;
    }
    
    // УДАЛИТЬ ИСТЁКШЕ ПРОМО КОДЫ
    public static function removeExpirePincode($date)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'sales WHERE type = 9 AND finish < :date';
        $result = $db->prepare($sql);
        $result->bindParam(':date', $date, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * СОХРАНИТ ДАННЫЕ ДЛЯ ПРОМО
     * @param $id
     * @param $promo_enable
     * @param $duration
     * @param $promo_word
     * @param $type_discount
     * @param $discount
     * @param $promo_products
     * @param $promo_gen
     * @param $desc
     * @return bool
     */
    public static function addPromoGen($id, $promo_enable, $duration, $promo_word, $type_discount, $discount, $promo_products,
        $promo_gen, $desc)
    {
        $db = Db::getConnection();
        if ($promo_gen) {
            $sql = 'UPDATE '.PREFICS.'products_promo SET duration = :duration, promo_word = :promo_word, type_discount = :type_discount,
                    discount = :discount, products = :products, status = :status, promo_desc = :promo_desc WHERE product_id = :product_id';
        } else {
            $sql = 'INSERT INTO '.PREFICS.'products_promo (product_id, duration, promo_word, type_discount, discount, products, status, promo_desc ) 
                    VALUES (:product_id, :duration, :promo_word, :type_discount, :discount, :products, :status, :promo_desc)';
        }

        $result = $db->prepare($sql);
        $result->bindParam(':product_id', $id, PDO::PARAM_INT);
        $result->bindParam(':duration', $duration, PDO::PARAM_INT);
        $result->bindParam(':promo_word', $promo_word, PDO::PARAM_STR);
        $result->bindParam(':type_discount', $type_discount, PDO::PARAM_STR);
        $result->bindParam(':discount', $discount, PDO::PARAM_INT);
        $result->bindParam(':products', $promo_products, PDO::PARAM_STR);
        $result->bindParam(':status', $promo_enable, PDO::PARAM_INT);
        $result->bindParam(':promo_desc', $desc, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ / ПРОВЕРИТЬ АВТОПРОМО КОДЫ ДЛЯ ПРОДУКТА
     * @param $id
     * @param null $status
     * @return bool|mixed
     */
    public static function getAutoPromoByID($id, $status = null)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."products_promo WHERE product_id = $id";
        $query .= ($status != null ? " AND status = $status" : '').' LIMIT 1';

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * @param $promo
     * @param $date
     * @param $rand_str
     * @param $client_email
     * @return bool
     */
    public static function createCoupon($promo, $date, $rand_str, $client_email) {
        $finish = $date + ($promo['duration'] * 86400);
        $promo_code = $promo['promo_word'].$rand_str;

        $add_promo = Product::addSale($rand_str, 9, $promo['promo_desc'], $date, $finish, 1,
            $promo['discount'], $promo['type_discount'], $promo_code, 1,
            0, null, $promo['products'], $client_email
        );

        if ($add_promo) {
            self::removeExpirePincode($date);
        }

        return $add_promo;
    }


    // УДАЛЕНИЕ СОПУТ ПРОДУКТА
    public static function deleteRelatedProduct($related_id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'products_related WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $related_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // СОХРАНЕНИЕ ПРОДУКТА ДЛЯ КОРЗИНЫ
    public static function saveRelatedProduct($item_id, $price, $sort, $show_complects, $status, $related_desc = 1)
    {
        $db = Db::getConnection();  
        if($related_desc == 1) {
            $sql = 'UPDATE '.PREFICS.'products_related SET price = :price, show_complect = :show_complect, status = :status, sort = :sort WHERE id = '.$item_id;
        } else {
             $sql = 'UPDATE '.PREFICS.'products_related SET price = :price, offer_desc = :offer_desc, show_complect = :show_complect, status = :status, sort = :sort WHERE id = '.$item_id;
        }
        
        $result = $db->prepare($sql);
        if($related_desc != 1) $result->bindParam(':offer_desc', $related_desc, PDO::PARAM_STR);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':show_complect', $show_complects, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ДОБАВЛЕНИЕ ПРОДУКТА ДЛЯ ПОКАЗА В КОРЗИНЕ
    public static function addRelatedProduct($id, $product_related, $price, $show_complects, $status, $related_desc, $sort)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'products_related (base_id, product_id, price, offer_desc, show_complect, status, sort ) 
                VALUES (:base_id, :product_id, :price, :offer_desc, :show_complect, :status, :sort )';
        
        $result = $db->prepare($sql);
        $result->bindParam(':base_id', $id, PDO::PARAM_INT);
        $result->bindParam(':product_id', $product_related, PDO::PARAM_INT);
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':offer_desc', $related_desc, PDO::PARAM_STR);
        $result->bindParam(':show_complect', $show_complects, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // СПИСОК ПРОДУКТОВ ДЛЯ КОРЗИНЫ
    public static function getRelatedProductsByID($id, $status = null)
    {
        $db = Db::getConnection();
        if($status == null) $result = $db->query("SELECT * FROM ".PREFICS."products_related WHERE base_id = $id ORDER BY sort ASC");
        else $result = $db->query("SELECT * FROM ".PREFICS."products_related WHERE base_id = $id AND status = 1 ORDER BY sort ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['base_id'] = $row['base_id'];
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['price'] = $row['price'];
            $data[$i]['offer_desc'] = $row['offer_desc'];
            $data[$i]['show_complect'] = $row['show_complect'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['sort'] = $row['sort'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ДАННЫЕ СОПУТСВУЮЩЕГО ПРОДУКТА ДЛЯ КОРЗИНЫ
    public static function getRelatedItemByID($item_id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."products_related WHERE id = $item_id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК КОМПЛЕКТАЦИЙ ДЛЯ БАЗОВОГО ПРОДУКТА
    public static function getComplectList($product_id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."products WHERE base_id = $product_id ORDER BY complect_sort ASC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['product_name'] = $row['product_name'];
            $data[$i]['complect_params'] = $row['complect_params'];
            $data[$i]['price_layout'] = $row['price_layout'];
            $data[$i]['complect_sort'] = $row['complect_sort'];
            $data[$i]['product_amt'] = $row['product_amt'];
            $data[$i]['show_amt'] = $row['show_amt'];
            $data[$i]['button_text'] = $row['button_text'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    // СПИСОК МЕТОК ДЛЯ ОТЗЫВОВ
    public static function getReviewsLabels()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."reviews_labels ORDER BY label_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['label_id'] = $row['label_id'];
            $data[$i]['label_name'] = $row['label_name'];
            $data[$i]['create_date'] = $row['create_date'];
            $data[$i]['status'] = $row['status'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ДОБАВИТЬ МЕТКУ ОТЗЫВА
    public static function addLabelReview($name, $alias, $status)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'reviews_labels (label_name, label_alias, status ) 
                VALUES (:name, :alias, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ МЕТКУ ОТЗЫВА
    public static function editLabelReview($id, $name, $alias, $status)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'reviews_labels SET label_name = :name, label_alias = :alias, status = :status WHERE label_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ДАННЫЕ МЕТКИ ОТЗЫВА
    public static function getReviewsLabelByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."reviews_labels WHERE label_id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // УДАЛИТЬ МЕТКУ ОТЗЫВА
    public static function deleteLabel($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'reviews_labels WHERE label_id = :id; DELETE FROM '.PREFICS.'reviews_labels_map WHERE label_id = :id;';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПОДСЧЁТ ОТЗЫВОВ
    public static function countReviews($status = null)
    {
        $db = Db::getConnection();
        if($status != null)$result = $db->query("SELECT COUNT(id) FROM ".PREFICS."reviews WHERE status = 1");
        else $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."reviews");
        $count = $result->fetch();
        return $count[0];
    }
    
    
    // СПИСОК ОТЗЫВОВ ПО ПРОДУКТУ
    public static function getReviewsByProductID($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."reviews WHERE status = 1 AND product_id = $id ORDER BY id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['text'] = $row['text'];
            $data[$i]['rate'] = $row['rate'];
            $data[$i]['attach'] = $row['attach'];
            $data[$i]['site_url'] = $row['site_url'];
            $data[$i]['vk_url'] = $row['vk_url'];
            $data[$i]['fb_url'] = $row['fb_url'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК ОТЗЫВОВ
    public static function getReviews($status, $cat = null, $label = null, $count = null, $page = 1)
    {
        $offset = ($page - 1) * $count;
        
        $db = Db::getConnection();
        $false = true;
        
        $where = '';
        if(is_numeric($status)) $where = self::addWhere($where, " status = $status ");
        if(!empty($cat)) {
            $where = self::addWhere($where, " cat_id = $cat ");
            $false = false;
            }
        if(!empty($label)) $where = self::addWhere($where, " id IN (SELECT review_id FROM ".PREFICS."reviews_labels_map WHERE label_id = $label) ", $false);
        
        if($count != null) $count = " LIMIT $count";
        
        $sql = "SELECT * FROM ".PREFICS."reviews";
        
        if($where) $sql .= " WHERE $where ORDER BY id DESC $count OFFSET $offset";
        else $sql .= " ORDER BY id DESC $count OFFSET $offset";
        
        //echo $sql;
        //exit;
        
        $result = $db->query("$sql");
        
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['id'] = $row['id'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['name'] = $row['name'];
            $data[$i]['text'] = $row['text'];
            $data[$i]['rate'] = $row['rate'];
            $data[$i]['attach'] = $row['attach'];
            $data[$i]['site_url'] = $row['site_url'];
            $data[$i]['vk_url'] = $row['vk_url'];
            $data[$i]['fb_url'] = $row['fb_url'];
            $data[$i]['status'] = $row['status'];
			$data[$i]['product_id'] = $row['product_id'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ДАННЫЕ ОТЗЫВА
    public static function getReviewByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."reviews WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // РЕДАКТИРОВАТЬ ОТЗЫВ
    public static function editReview($id, $name, $email, $cat_id, $text, $site_url, $vk_url, $fb_url, $rate, $status, $img, $product_id)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'reviews SET name = :name, cat_id = :cat_id, product_id = :product_id, email = :email, text = :text, rate = :rate, site_url = :site_url, vk_url = :vk_url, fb_url = :fb_url, status = :status, attach = :attach WHERE id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':text', $text, PDO::PARAM_STR);
        $result->bindParam(':site_url', $site_url, PDO::PARAM_STR);
        $result->bindParam(':vk_url', $vk_url, PDO::PARAM_STR);
        $result->bindParam(':fb_url', $fb_url, PDO::PARAM_STR);
        $result->bindParam(':attach', $img, PDO::PARAM_STR);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':rate', $rate, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ МЕТКИ ОТЗЫВА
    public static function getLabelMap($id)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT label_id FROM ".PREFICS."reviews_labels_map WHERE review_id = $id ORDER BY id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i] = $row['label_id'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
        
    }
    
    // ЗАПИСАТЬ МЕТКИ ОТЗЫВА В КАРТУ
    public static function labelWriteMap($id, $label_list)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'reviews_labels_map WHERE review_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $res = $result->execute();
        
        foreach($label_list as $label){
            
            $sql = 'INSERT INTO '.PREFICS.'reviews_labels_map (label_id, review_id ) 
                VALUES (:label_id, :review_id)';
        
            $result = $db->prepare($sql);
            $result->bindParam(':label_id', $label, PDO::PARAM_INT);
      		$result->bindParam(':review_id', $id, PDO::PARAM_INT);
            $res = $result->execute();
        }
        
        return $res;
    }
    
    
    // СПИСОК КАТЕГОРИЙ ОТЗЫВОВ
    public static function getReviewsCats()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."reviews_cats ORDER BY cat_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['cat_name'] = $row['cat_name'];
            $data[$i]['create_date'] = $row['create_date'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СОЗДАТЬ КАТЕГОРИЮ ОТЗЫВА
    public static function addReviewCat($name, $status, $alias)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'reviews_cats (cat_name, cat_alias, status ) 
                VALUES (:name, :alias, :status)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ПРОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ ВИДЖЕТА
    public static function getReviewCatByID($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."reviews_cats WHERE cat_id = $id LIMIT 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ ОТЗЫВА
    public static function editReviewCat($id, $name, $status, $alias)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'reviews_cats SET cat_name = :name, status = :status, cat_alias = :alias WHERE cat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':alias', $alias, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // ДОБАВИТЬ ОТЗЫВ
    public static function addReview($time, $name, $email, $site_url, $vk_url, $fb_url, $review, $range, $img)
    {
        $db = Db::getConnection();
        $status = 0;
        $sql = 'INSERT INTO '.PREFICS.'reviews (name, email, text, rate, status, attach, site_url, vk_url, fb_url  ) 
                VALUES (:name, :email, :text, :rate, :status, :attach, :site_url, :vk_url, :fb_url)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':text', $review, PDO::PARAM_STR);
        $result->bindParam(':rate', $range, PDO::PARAM_INT);
        $result->bindParam(':attach', $img, PDO::PARAM_STR);
        $result->bindParam(':site_url', $site_url, PDO::PARAM_STR);
        $result->bindParam(':vk_url', $vk_url, PDO::PARAM_STR);
        $result->bindParam(':fb_url', $fb_url, PDO::PARAM_STR);
  		$result->bindParam(':status', $status, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ КАТЕГОРИЮ ВИДЖЕТА
    public static function deleteReviewCat($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query(" SELECT id FROM ".PREFICS."reviews WHERE cat_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return false;
        else {
        
            $sql = 'DELETE FROM '.PREFICS.'reviews_cats WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();
        }
    }
    
    
    
    // УДАЛИТЬ ОТЗЫВ
    public static function deleteReview($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."reviews WHERE id = $id LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) {
    
            if(!empty($data['attach'])) {            
                $path = ROOT.'/images/reviews/'.$data['attach'];
                if(file_exists($path)){
                    unlink ($path);
                }
            }
            
            $sql = 'DELETE FROM '.PREFICS.'reviews WHERE id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();
            
        }
    
    }
    
    
    
    
    // СПИСОК ПРОДУКТОВ ДЛЯ АДМИНКИ
    public static function getAdminProductList($category = null, $type = null, $status = null, $sort = 1)
    {
        $db = Db::getConnection();
        if($status != null){
            if($status == 'off') $stat = 0;
            elseif($status == 'arch') $stat = 9;
            elseif($status == 'on') $stat = 1;
            else 
            $stat = 1;
        }

        if($sort == 1) $sort = 'ASC';
        else $sort = 'DESC';

        if($category != null && $type != null && $status == null) $result = $db->query("SELECT * FROM ".PREFICS."products WHERE cat_id = $category AND type_id = $type ORDER BY sort $sort, product_id DESC");
        elseif($category != null) $result = $db->query("SELECT * FROM ".PREFICS."products WHERE cat_id = $category ORDER BY sort $sort, product_id DESC");
        elseif($type != null) $result = $db->query("SELECT * FROM ".PREFICS."products WHERE type_id = $type ORDER BY sort $sort, product_id DESC");
        elseif($status != null) $result = $db->query("SELECT * FROM ".PREFICS."products WHERE status = $stat ORDER BY sort $sort, product_id DESC");
        else $result = $db->query("SELECT * FROM ".PREFICS."products WHERE status <> 9 ORDER BY sort $sort, product_id DESC");
        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['product_name'] = $row['product_name'];
            $data[$i]['product_cover'] = $row['product_cover'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['type_id'] = $row['type_id'];
            $data[$i]['price'] = $row['price'];
            $data[$i]['red_price'] = $row['red_price'];
            $data[$i]['status'] = $row['status'];
            $data[$i]['base_id'] = $row['base_id'];
            $data[$i]['service_name'] = $row['service_name'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    
    // СПИСОК ПРОДУКТОВ ДЛЯ КАТАЛОГА
    public static function getProductInCatalog($category = null)
    {
        $db = Db::getConnection();
        if($category == null){
            $result = $db->query("SELECT * FROM ".PREFICS."products WHERE status = 1 AND in_catalog = 1 ORDER BY sort ASC");            
        } else {
            
            $result = $db->query("SELECT * FROM ".PREFICS."products WHERE status = 1 AND in_catalog = 1 AND cat_id = $category ORDER BY sort ASC");
        }

        $i = 0;
        while($row = $result->fetch()){
            $data[$i]['product_id'] = $row['product_id'];
            $data[$i]['product_name'] = $row['product_name'];
            $data[$i]['product_cover'] = $row['product_cover'];
            $data[$i]['product_alias'] = $row['product_alias'];
            $data[$i]['img_alt'] = $row['img_alt'];
            $data[$i]['product_amt'] = $row['product_amt'];
            $data[$i]['product_desc'] = $row['product_desc'];
            $data[$i]['cat_id'] = $row['cat_id'];
            $data[$i]['type_id'] = $row['type_id'];
            $data[$i]['price'] = $row['price'];
            $data[$i]['red_price'] = $row['red_price'];
            $data[$i]['button_text'] = $row['button_text'];
            $data[$i]['show_price_box'] = $row['show_price_box'];
            $data[$i]['external_landing'] = $row['external_landing'];
            $data[$i]['external_url'] = $row['external_url'];
			$data[$i]['hidden_price'] = $row['hidden_price'];
            $i++;
        }
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }


    /**
     * ПОЛУЧИТЬ ПРОДУКТЫ ДЛЯ ЗАКАЗА
     * @param $order_id
     * @return array|bool
     */
    public static function getProducts2Order($order_id) {
        $db = Db::getConnection();
        $query = "SELECT p.* FROM ".PREFICS."products AS p
                  INNER JOIN ".PREFICS."order_items AS oi ON oi.product_id = p.product_id
                  WHERE oi.order_id = $order_id ORDER BY order_id DESC";
        $result = $db->query($query);

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    // СПИСОК ПРОДУКТОВ ДЛЯ КОРЗИНЫ
    public static function getProductsByIds($idsArray)
    {
        $products = array();
        $db = Db::getConnection();
        
        $idsString = implode(',', $idsArray);
        
        $sql = "SELECT product_id, product_name, product_desc, product_cover, img_alt, type_id, base_id FROM ".PREFICS."products WHERE status = 1 AND product_id IN ($idsString)";
        
        $result = $db->query($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        
        $i = 0;
        while ($row = $result->fetch()) {
            $products[$i]['product_id'] = $row['product_id'];
            $products[$i]['product_name'] = $row['product_name'];
            $products[$i]['product_desc'] = $row['product_desc'];
            $products[$i]['product_cover'] = $row['product_cover'];
            $products[$i]['img_alt'] = $row['img_alt'];
            $products[$i]['type_id'] = $row['type_id'];
            $products[$i]['base_id'] = $row['base_id'];
            $i++;  
        }
        
        return $products;
        
    }


    /**
     * СОЗДАТЬ АКЦИЮ
     * @param $name
     * @param $type
     * @param $desc
     * @param $start
     * @param $finish
     * @param $status
     * @param $discount
     * @param $discount_type
     * @param $promo
     * @param $promo_calc_discount
     * @param $partner_id
     * @param $duration
     * @param $products
     * @param null $email
     * @return bool
     */
    public static function addSale($name, $type, $desc, $start, $finish, $status, $discount, $discount_type, $promo,
                                   $promo_calc_discount, $partner_id, $duration, $products, $email = null)
    {
        $db = Db::getConnection();

        $sql = 'INSERT INTO '.PREFICS.'sales (name, sale_desc, type, start, finish, promo_code, promo_calc_discount, 
                    partner_id, discount, discount_type, duration, status, products'.($email != null ? ', client_email' : '').')
                VALUES (:name, :sale_desc, :type, :start, :finish, :promo_code, :promo_calc_discount, :partner_id, :discount,
                 :discount_type, :duration, :status, :products'.($email != null ? ', :email' : '') . ')';
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':sale_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        $result->bindParam(':start', $start, PDO::PARAM_INT);
        $result->bindParam(':finish', $finish, PDO::PARAM_INT);
        $result->bindParam(':promo_code', $promo, PDO::PARAM_STR);
        $result->bindParam(':promo_calc_discount', $promo_calc_discount, PDO::PARAM_INT);
        $result->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $result->bindParam(':discount', $discount, PDO::PARAM_INT);
        $result->bindParam(':discount_type', $discount_type, PDO::PARAM_STR);
        $result->bindParam(':duration', $duration, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':products', $products, PDO::PARAM_STR);

        if ($email != null) {
            $result->bindParam(':email', $email, PDO::PARAM_STR);
        }

        return $result->execute();
    }
    
    
    
    // ИЗМЕНИТЬ АКЦИЮ
    public static function editSale($id, $name, $type, $desc, $start, $finish, $status, $discount, $discount_type, $promo,
                                    $promo_calc_discount, $partner_id, $duration, $product)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'sales SET name = :name, sale_desc = :sale_desc, type = :type, start = :start, finish = :finish, 
                            promo_code = :promo_code, promo_calc_discount = :promo_calc_discount, partner_id = :partner_id,
                            discount = :discount, discount_type = :discount_type, duration = :duration, status = :status,
                            products = :products WHERE id = '.$id;
        
        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->bindParam(':sale_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':type', $type, PDO::PARAM_INT);
        $result->bindParam(':start', $start, PDO::PARAM_INT);
        $result->bindParam(':finish', $finish, PDO::PARAM_INT);
        $result->bindParam(':promo_code', $promo, PDO::PARAM_STR);
        $result->bindParam(':promo_calc_discount', $promo_calc_discount, PDO::PARAM_INT);
        $result->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $result->bindParam(':discount', $discount, PDO::PARAM_INT);
        $result->bindParam(':discount_type', $discount_type, PDO::PARAM_STR);
        $result->bindParam(':duration', $duration, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':products', $product, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    
    
    // СПИСОК ТОВАРОВ ПО АКЦИИ КРАСНАЯ ЦЕНА
    public static function getSaleProduct()
    {
        // получить список действующих акций
        $array_merge = array();
        $time = time();
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."sales WHERE type = 1 AND status = 1 AND start < $time AND finish > $time ORDER BY id DESC");
    
        $data = [];
        while ($row = $result->fetch()){
            $data[] = [
                'id' => $row['id'],
                'products' => $row['products'],
            ];
        }
        
        if (!empty($data)) {
            foreach($data as $sale){
                // Соединяем массивы с id продуктов
                $array_merge = array_merge($array_merge, unserialize($sale['products']));
            }
            
            $array_merge = array_unique($array_merge); // убираем повторяющиея id 
            $str = implode(",", $array_merge);
            
            $result = $db->query("SELECT * FROM ".PREFICS."products WHERE in_catalog = 1 AND product_id IN ($str) ORDER BY product_id DESC");
            
            $data = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            
            return !empty($data) ? $data : false;
        } else {
            return false;
        }
    }
    
    
    // СОХРАНИТЬ НАСТРОЙКИ СТРАНИЦЫ АКЦИЙ
    public static function saveSalePage($str, $text, $code)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'sales_page SET param = :param, page_text = :page_text, page_code = :page_code WHERE id = 1';
        $result = $db->prepare($sql);
        $result->bindParam(':param', $str, PDO::PARAM_STR);
        $result->bindParam(':page_text', $text, PDO::PARAM_STR);
        $result->bindParam(':page_code', $code, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ПОЛУЧИТЬ НАСТРОЙКИ СТРАНИЦЫ АКЦИЙ
    public static function getSalesPage()
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."sales_page WHERE id = 1 LIMIT 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // СПИСОК АКЦИЙ
    public static function getSaleList($status = 0, $types = [])
    {
        $db = Db::getConnection();
        
        $query = "SELECT * FROM ".PREFICS."sales";
    
        $clauses = [];
        if ($status != 0) {
            $time = time();
            $clauses[] = "status = 1 AND start < $time AND finish > $time";
        }
        
        if ($types) {
            $clauses[] = 'type in (' . implode(',', $types) . ')';
        }
    
        $where = !empty($clauses) ? (' WHERE ' . implode(' AND ', $clauses)) : '';
        $query .= "$where ORDER BY " . ($status != 0 ? 'type' : 'id') . ' DESC';
        
        $result = $db->query($query);
        
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        return !empty($data) ? $data : false;
    }
    
    
    // ДАННЫЕ АКЦИИ
    public static function getSaleData($id, $promo_name = null)
    {
        $db = Db::getConnection();
        $where = $id ? "id = $id" : "name = '$promo_name'";
        $sql = "SELECT * FROM ".PREFICS."sales WHERE $where";

        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    
    // УДАЛИТЬ АКЦИЮ
    public static function deleteSale($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'sales WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * СПИСОК ПРОДУКТОВ ДЛЯ ВЫБОРА КОМПЛЕКТАЦИЙ в select
     * @return bool
     */
    public static function getProductListOnlySelect()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT product_id, product_name, service_name, price, run_aff FROM ".PREFICS."products ORDER BY product_id DESC");
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ДАННЫЕ ПРОДУКТА ПО ID (для админки и т.д.)
    public static function getProductById($id)
    {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS."products  WHERE product_id = $id";
        
        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        return !empty($data) ? $data : false;
    }


    /**
     * ДАННЫЕ ПРОДУКТА ПО ID для клиента
     * @param $id
     * @param int $status
     * @return bool|mixed
     */
    public static function getProductData($id, $status = 1)
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM ".PREFICS."products WHERE product_id = $id";
        $sql .= $status ? " AND status = $status" : '';
        $result = $db->query($sql);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }
    
    
    
    // ОБНОВИТЬ КОЛ_ВО ПРОДУКТА
    public static function updateAmt($id, $count)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'products SET product_amt = :product_amt WHERE product_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':product_amt', $count, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    
    // СБРОСИТЬ СПЛИТ ТЕСТ
    public static function resetSplit($id)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM '.PREFICS.'split_tests WHERE product_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }


    // ОБНОВИТЬ СОРТИРОВКУ ДЛЯ ПРОДУКТА (SORT)
    public static function UpdateSortProduct($prod_id, $sort)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'products SET sort = :sort WHERE product_id = :product_id';
        $result = $db->prepare($sql);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':product_id', $prod_id, PDO::PARAM_INT);
        return $result->execute();
    }


    // ОСНОВНЫЕ ДАННЫЕ ПРОДУКТА ПО ID для клиента при отсылке заказа
    public static function getProductDataForSendOrder($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."products WHERE product_id = $id AND status = 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ДАННЫЕ ПРОДУКТА ПО ID для апселла
    // ПОЛУЧАЕТ ДАННЫЕ ПРОДУКТОВ ДЛЯ АПСЕЛЛА: тексты, описание, цену
    public static function getProductUpsellData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT type_id, upsell_1, upsell_2, upsell_3, upsell_1_desc, upsell_2_desc, upsell_3_desc, 
                                        upsell_1_text, upsell_2_text, upsell_3_text, upsell_1_price, upsell_2_price, upsell_3_price 
                                        FROM ".PREFICS."products WHERE product_id = $id AND status = 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // КРАТКИЕ ДАННЫЕ ПРОДУКТА ПО ID (для комплектаций.)
    public static function getMinProductById($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT product_name, link, product_cover, img_alt, show_amt, product_amt, price, installment FROM ".PREFICS."products WHERE product_id = $id AND status = 1");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ИМЯ продукта по ID 
	public static function getProductName($id)
    {
        $id = intval($id);
        $db = Db::getConnection();
        $result = $db->query(" SELECT product_name, service_name, status, type_id, group_id, link FROM ".PREFICS."products WHERE product_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        // 
        if(isset($data) && !empty($data) && $data['status'] == 1 && $data['type_id'] == 1){
            $data['dwl'] = 1;  
            $data['mess'] = ''; 
            return $data;
        }
        elseif(isset($data) && !empty($data) && $data['status'] == 0) {
            $data['mess'] = ' <br /><span class="small red">(снят с продаж)</span>';
            $data['dwl'] = 0;   
            return $data;
        }
        elseif(isset($data) && !empty($data) && $data['type_id'] != 1) {
            $data['mess'] = '';
            $data['dwl'] = 1;   
            return $data;
        } else {
            $data['mess'] = 'Продукт больше не существует';
            $data['dwl'] = 0;
            $data['product_name'] = '- - ';
            $data['service_name'] = '- - ';
			$data['group_id'] = false;
            return $data;
        }
    }
    
    
    // ДАННЫЕ ПРОДУКТА ПО АЛИАСУ, если нет, то возвращаем false
    public static function getProductDataByAlias($alias)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."products WHERE product_alias = '$alias' AND status = 1 ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
        else return false;
    }
    
    
    // ОБНОВИТЬ ХИТЫ
    public static function updateHits($id, $variant, $hits)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'products SET '.$variant.' = :hits WHERE product_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':hits', $hits, PDO::PARAM_INT);
        return $result->execute();
    }


    /**
     * ДОБАВИТЬ ПРОДУКТ
     * @param $name
     * @param $service_name
     * @param $title
     * @param $cat_id
     * @param $price
     * @param $red_price
     * @param $product_type
     * @param $amt
     * @param $show_amt
     * @param $link
     * @param $subscription
     * @param $desc
     * @param $in_catalog
     * @param $in_partner
     * @param $status
     * @param $alias
     * @param $meta_desc
     * @param $meta_keys
     * @param $delivery
     * @param $delivery_unsub
     * @param $add_group
     * @param $del_group
     * @param $letter
     * @param $subject_letter
     * @param $text1
     * @param $text2
     * @param $notif_url
     * @param $author1
     * @param $author2
     * @param $author3
     * @param $base_id
     * @param $img_alt
     * @param $img
     * @param $text1_tmpl
     * @param $text2_tmpl
     * @param $pincodes
     * @param $upsell_1
     * @param $upsell_2
     * @param $upsell_3
     * @param $upsell_1_desc
     * @param $upsell_2_desc
     * @param $upsell_3_desc
     * @param $upsell_1_text
     * @param $upsell_2_text
     * @param $upsell_3_text
     * @param $upsell_1_price
     * @param $upsell_2_price
     * @param $upsell_3_price
     * @param $text1_head
     * @param $text2_head
     * @param $text1_bottom
     * @param $text2_bottom
     * @param $text1_heading
     * @param $text2_heading
     * @param $button_text
     * @param $show_price_box
     * @param $code_price_box
     * @param $complect_params
     * @param $price_layout
     * @param $complect_sort
     * @param $show_reviews
     * @param $sell_once
     * @param $external_landing
     * @param $external_url
     * @param $run_aff
     * @param $product_comiss
     * @param $send_pass
     * @param $redirect_after
     * @param $manager_letter
     * @param $hidden_price
     * @param $price_minmax
     * @param $to_resale
     * @param $select_payments
     * @return bool
     */
    public static function AddProduct($name, $service_name, $title, $cat_id, $price, $red_price, $product_type, $amt, $show_amt, $link,
                                      $subscription, $desc, $in_catalog, $in_partner, $status, $alias, $meta_desc, $meta_keys, $delivery,
                                      $delivery_unsub, $add_group, $del_group, $letter, $subject_letter, $text1, $text2, $notif_url, $author1,
                                      $author2, $author3, $base_id, $img_alt, $img, $text1_tmpl, $text2_tmpl, $pincodes, $upsell_1, $upsell_2,
                                      $upsell_3, $upsell_1_desc, $upsell_2_desc, $upsell_3_desc, $upsell_1_text, $upsell_2_text, $upsell_3_text,
                                      $upsell_1_price, $upsell_2_price, $upsell_3_price, $text1_head, $text2_head, $text1_bottom, $text2_bottom,
                                      $text1_heading, $text2_heading, $button_text, $show_price_box, $code_price_box, $complect_params,
                                      $price_layout, $complect_sort, $show_reviews, $sell_once, $external_landing, $external_url, $run_aff, $product_comiss,
                                      $send_pass, $redirect_after, $manager_letter, $hidden_price, $price_minmax, $to_resale, $select_payments)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'products (product_name, service_name, product_alias, product_title, product_cover, img_alt, link, product_amt,
                    show_amt, letter, letter_subject, cat_id, type_id, product_desc, delivery_unsub, product_text1, product_text2, price, red_price,
                    in_catalog, in_partner, group_id, del_group_id, delivery_sub, notif_url, author1, author2, author3, subscription_id, meta_desc,
                    meta_keys, base_id, status, text1_tmpl, text2_tmpl, pincodes, upsell_1, upsell_2, upsell_3, upsell_1_desc, upsell_2_desc,
                    upsell_3_desc, upsell_1_text, upsell_2_text, upsell_3_text, upsell_1_price, upsell_2_price, upsell_3_price, text1_head, text2_head,
                    text1_bottom, text2_bottom, text1_heading, text2_heading, button_text, show_price_box, code_price_box, complect_params, price_layout,
                    complect_sort, show_reviews, sell_once, hits_1, hits_2, external_landing, external_url, run_aff, product_comiss, send_pass, redirect_after,
                    manager_letter, hidden_price, price_minmax, to_resale, select_payments) 
                VALUES (:product_name, :service_name, :product_alias, :product_title, :product_cover, :img_alt, :link, :product_amt, :show_amt, :letter,
                    :subject_letter, :cat_id, :type_id, :product_desc, :delivery_unsub,
                    :product_text1, :product_text2, :price, :red_price, :in_catalog, :in_partner, :group_id, :del_group_id, :delivery_sub, :notif_url,
                    :author1, :author2, :author3, :subscription_id, :meta_desc, :meta_keys, :base_id, :status, :text1_tmpl, :text2_tmpl, :pincodes,
                    :upsell_1, :upsell_2, :upsell_3, :upsell_1_desc, :upsell_2_desc, :upsell_3_desc, :upsell_1_text, :upsell_2_text, :upsell_3_text, 
                    :upsell_1_price, :upsell_2_price, :upsell_3_price, :text1_head, :text2_head, :text1_bottom, :text2_bottom, :text1_heading,
                    :text2_heading, :button_text, :show_price_box, :code_price_box, :complect_params, :price_layout, :complect_sort,
                    :show_reviews, :sell_once, 0, 0, :external_landing, :external_url, :run_aff, :product_comiss, :send_pass, :redirect_after,
                    :manager_letter, :hidden_price, :price_minmax, :to_resale, :select_payments)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':product_name', $name, PDO::PARAM_STR);
        $result->bindParam(':service_name', $service_name, PDO::PARAM_STR);
        $result->bindParam(':product_title', $title, PDO::PARAM_STR);
        $result->bindParam(':product_alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':product_cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        
        $result->bindParam(':manager_letter', $manager_letter, PDO::PARAM_STR);
        
        $result->bindParam(':external_url', $external_url, PDO::PARAM_STR);
        $result->bindParam(':external_landing', $external_landing, PDO::PARAM_INT);
        $result->bindParam(':product_comiss', $product_comiss, PDO::PARAM_INT);
        
        $result->bindParam(':redirect_after', $redirect_after, PDO::PARAM_STR);
        $result->bindParam(':send_pass', $send_pass, PDO::PARAM_INT);
        
        $result->bindParam(':complect_params', $complect_params, PDO::PARAM_STR);
        $result->bindParam(':price_layout', $price_layout, PDO::PARAM_INT);
        $result->bindParam(':complect_sort', $complect_sort, PDO::PARAM_INT);
        $result->bindParam(':show_reviews', $show_reviews, PDO::PARAM_INT);
        $result->bindParam(':sell_once', $sell_once, PDO::PARAM_INT);
        
        $result->bindParam(':button_text', $button_text, PDO::PARAM_STR);
        
        $result->bindParam(':link', $link, PDO::PARAM_STR);
        $result->bindParam(':product_amt', $amt, PDO::PARAM_INT);
        
        $result->bindParam(':show_amt', $show_amt, PDO::PARAM_INT);
        $result->bindParam(':letter', $letter, PDO::PARAM_STR);
		$result->bindParam(':subject_letter', $subject_letter, PDO::PARAM_STR);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':type_id', $product_type, PDO::PARAM_INT);
        $result->bindParam(':product_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':product_text1', $text1, PDO::PARAM_STR);
        $result->bindParam(':product_text2', $text2, PDO::PARAM_STR);
        
        $result->bindParam(':text1_head', $text1_head, PDO::PARAM_STR);
        $result->bindParam(':text2_head', $text2_head, PDO::PARAM_STR);
        $result->bindParam(':text1_bottom', $text1_bottom, PDO::PARAM_STR);
        $result->bindParam(':text2_bottom', $text2_bottom, PDO::PARAM_STR);
        $result->bindParam(':run_aff', $run_aff, PDO::PARAM_INT);
        
        $result->bindParam(':text1_tmpl', $text1_tmpl, PDO::PARAM_INT);
        $result->bindParam(':text2_tmpl', $text2_tmpl, PDO::PARAM_INT);
        
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':price_minmax', $price_minmax, PDO::PARAM_STR);
        $result->bindParam(':red_price', $red_price, PDO::PARAM_INT);
        $result->bindParam(':in_catalog', $in_catalog, PDO::PARAM_INT);
        $result->bindParam(':in_partner', $in_partner, PDO::PARAM_INT);
        $result->bindParam(':group_id', $add_group, PDO::PARAM_STR);
        $result->bindParam(':del_group_id', $del_group, PDO::PARAM_STR);
        $result->bindParam(':delivery_sub', $delivery, PDO::PARAM_STR);
        $result->bindParam(':delivery_unsub', $delivery_unsub, PDO::PARAM_STR);
        
        $result->bindParam(':notif_url', $notif_url, PDO::PARAM_STR);
        $result->bindParam(':author1', $author1, PDO::PARAM_STR);
        $result->bindParam(':author2', $author2, PDO::PARAM_STR);
        $result->bindParam(':author3', $author3, PDO::PARAM_STR);
        $result->bindParam(':subscription_id', $subscription, PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        
        $result->bindParam(':base_id', $base_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        
        $result->bindParam(':pincodes', $pincodes, PDO::PARAM_STR);
        $result->bindParam(':upsell_1', $upsell_1, PDO::PARAM_INT);
        $result->bindParam(':upsell_2', $upsell_2, PDO::PARAM_INT);
        $result->bindParam(':upsell_3', $upsell_3, PDO::PARAM_INT);
        
        $result->bindParam(':upsell_1_price', $upsell_1_price, PDO::PARAM_INT);
        $result->bindParam(':upsell_2_price', $upsell_2_price, PDO::PARAM_INT);
        $result->bindParam(':upsell_3_price', $upsell_3_price, PDO::PARAM_INT);
        
        $result->bindParam(':upsell_1_desc', $upsell_1_desc, PDO::PARAM_STR);
        $result->bindParam(':upsell_2_desc', $upsell_2_desc, PDO::PARAM_STR);
        $result->bindParam(':upsell_3_desc', $upsell_3_desc, PDO::PARAM_STR);
        $result->bindParam(':upsell_1_text', $upsell_1_text, PDO::PARAM_STR);
        $result->bindParam(':upsell_2_text', $upsell_2_text, PDO::PARAM_STR);
        $result->bindParam(':upsell_3_text', $upsell_3_text, PDO::PARAM_STR);
        
        $result->bindParam(':text1_heading', $text1_heading, PDO::PARAM_INT);
        $result->bindParam(':text2_heading', $text2_heading, PDO::PARAM_INT);
        $result->bindParam(':show_price_box', $show_price_box, PDO::PARAM_INT);
        $result->bindParam(':code_price_box', $code_price_box, PDO::PARAM_STR);
        $result->bindParam(':hidden_price', $hidden_price, PDO::PARAM_INT);
        $result->bindParam(':to_resale', $to_resale, PDO::PARAM_INT);
        $result->bindParam(':select_payments', $select_payments, PDO::PARAM_STR);
        
        return $result->execute();
    }


    /**
     * ИЗМЕНИТЬ ПРОДУКТ
     * @param $id
     * @param $name
     * @param $service_name
     * @param $title
     * @param $cat_id
     * @param $price
     * @param $red_price
     * @param $product_type
     * @param $amt
     * @param $show_amt
     * @param $link
     * @param $subscription
     * @param $desc
     * @param $in_catalog
     * @param $in_partner
     * @param $status
     * @param $alias
     * @param $meta_desc
     * @param $meta_keys
     * @param $delivery
     * @param $delivery_unsub
     * @param $add_group
     * @param $del_group
     * @param $letter
     * @param $subject_letter
     * @param $text1
     * @param $text2
     * @param $notif_url
     * @param $author1
     * @param $author2
     * @param $author3
     * @param $type_comiss1
     * @param $type_comiss2
     * @param $type_comiss3
     * @param $comiss1
     * @param $comiss2
     * @param $comiss3
     * @param $base_id
     * @param $img_alt
     * @param $img
     * @param $text1_tmpl
     * @param $text2_tmpl
     * @param $pincodes
     * @param $upsell_1
     * @param $upsell_2
     * @param $upsell_3
     * @param $upsell_1_desc
     * @param $upsell_2_desc
     * @param $upsell_3_desc
     * @param $upsell_1_text
     * @param $upsell_2_text
     * @param $upsell_3_text
     * @param $upsell_1_price
     * @param $upsell_2_price
     * @param $upsell_3_price
     * @param $zip
     * @param $text1_head
     * @param $text2_head
     * @param $text1_bottom
     * @param $text2_bottom
     * @param $text1_heading
     * @param $text2_heading
     * @param $button_text
     * @param $show_price_box
     * @param $code_price_box
     * @param $custom_code
     * @param $complect_params
     * @param $price_layout
     * @param $complect_sort
     * @param $show_reviews
     * @param $sell_once
     * @param $external_landing
     * @param $external_url
     * @param $run_aff
     * @param $acymailing
     * @param $auto_add
     * @param $product_comiss
     * @param $send_pass
     * @param $redirect_after
     * @param $manager_letter
     * @param $note
     * @param $installment
     * @param $installment_action
     * @param $installment_addgroups
     * @param $hidden_price
     * @param $price_minmax
     * @param $to_resale
     * @param $select_payments
     * @return bool
     */
    public static function EditProduct($id, $name, $service_name, $title, $cat_id, $price, $red_price, $product_type, $amt, $show_amt,
                                       $link, $subscription, $desc, $in_catalog, $in_partner, $status, $alias, $meta_desc, $meta_keys,
                                       $delivery, $delivery_unsub, $add_group, $del_group, $letter, $subject_letter, $text1, $text2,
                                       $notif_url, $author1, $author2, $author3, $type_comiss1, $type_comiss2, $type_comiss3, $comiss1,
                                       $comiss2, $comiss3, $base_id, $img_alt, $img, $text1_tmpl, $text2_tmpl, $pincodes, $upsell_1,
                                       $upsell_2, $upsell_3, $upsell_1_desc, $upsell_2_desc, $upsell_3_desc, $upsell_1_text, $upsell_2_text,
                                       $upsell_3_text, $upsell_1_price, $upsell_2_price, $upsell_3_price, $zip, $text1_head, $text2_head,
                                       $text1_bottom, $text2_bottom, $text1_heading, $text2_heading, $button_text, $show_price_box,
                                       $code_price_box, $custom_code, $complect_params, $price_layout, $complect_sort, $show_reviews,
                                       $sell_once, $external_landing, $external_url, $run_aff, $acymailing, $auto_add, $product_comiss,
                                       $send_pass, $redirect_after, $manager_letter, $note, $installment, $installment_action,
                                       $installment_addgroups, $hidden_price, $price_minmax, $to_resale, $select_payments)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'products SET product_name = :product_name, service_name = :service_name, product_alias = :product_alias,
                product_title = :product_title, product_cover = :product_cover, img_alt = :img_alt, link = :link, product_amt = :product_amt,
                show_amt = :show_amt, letter = :letter, letter_subject = :subject_letter, cat_id = :cat_id, product_desc = :product_desc,
                type_id = :type_id, product_text1 = :product_text1, product_text2 = :product_text2, price = :price, red_price = :red_price,
                in_catalog = :in_catalog, in_partner = :in_partner, group_id = :group_id, del_group_id = :del_group_id, delivery_sub = :delivery_sub,
                notif_url = :notif_url, author1 = :author1, author2 = :author2, author3 = :author3, type_comiss1 = :type_comiss1,
                type_comiss2 = :type_comiss2, type_comiss3 = :type_comiss3, comiss1 = :comiss1, comiss2 = :comiss2, comiss3 = :comiss3,
                delivery_unsub = :delivery_unsub, subscription_id = :subscription_id, meta_desc = :meta_desc, meta_keys = :meta_keys,
                base_id = :base_id, text1_tmpl = :text1_tmpl, text2_tmpl = :text2_tmpl, status = :status, pincodes = :pincodes, upsell_1 = :upsell_1,
                upsell_2 = :upsell_2, upsell_3 = :upsell_3, upsell_1_desc = :upsell_1_desc, upsell_2_desc = :upsell_2_desc,
                upsell_3_desc = :upsell_3_desc, upsell_1_text = :upsell_1_text, upsell_2_text = :upsell_2_text, upsell_3_text = :upsell_3_text,
                upsell_1_price = :upsell_1_price, upsell_2_price = :upsell_2_price, upsell_3_price = :upsell_3_price, ads = :ads,
                text1_head = :text1_head, text2_head = :text2_head, text1_bottom = :text1_bottom, text2_bottom = :text2_bottom,
                text1_heading = :text1_heading, text2_heading = :text2_heading, button_text = :button_text, show_price_box = :show_price_box,
                code_price_box = :code_price_box, custom_code = :custom_code, complect_params = :complect_params, price_layout = :price_layout,
                complect_sort = :complect_sort, show_reviews = :show_reviews, sell_once = :sell_once, external_landing = :external_landing,
                external_url = :external_url, run_aff = :run_aff, acymailing = :acymailing, auto_add = :auto_add, product_comiss = :product_comiss,
                send_pass = :send_pass, redirect_after = :redirect_after, manager_letter = :manager_letter, note = :note, installment = :installment,
                installment_action = :installment_action, installment_addgroups = :installment_addgroups, hidden_price = :hidden_price,
                price_minmax = :price_minmax, to_resale = :to_resale, select_payments = :select_payments WHERE product_id = '.$id;
        
        $result = $db->prepare($sql);
        $result->bindParam(':product_name', $name, PDO::PARAM_STR);
        $result->bindParam(':service_name', $service_name, PDO::PARAM_STR);
        $result->bindParam(':product_title', $title, PDO::PARAM_STR);
        $result->bindParam(':product_alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':product_cover', $img, PDO::PARAM_STR);
        $result->bindParam(':img_alt', $img_alt, PDO::PARAM_STR);
        $result->bindParam(':acymailing', $acymailing, PDO::PARAM_STR);
        $result->bindParam(':note', $note, PDO::PARAM_STR);
        
        $result->bindParam(':manager_letter', $manager_letter, PDO::PARAM_STR);
        
        $result->bindParam(':installment', $installment, PDO::PARAM_INT);
		$result->bindParam(':installment_addgroups', $installment_addgroups, PDO::PARAM_INT);
        $result->bindParam(':installment_action', $installment_action, PDO::PARAM_STR);
        
        $result->bindParam(':external_url', $external_url, PDO::PARAM_STR);
        $result->bindParam(':external_landing', $external_landing, PDO::PARAM_INT);
        $result->bindParam(':redirect_after', $redirect_after, PDO::PARAM_STR);
        $result->bindParam(':send_pass', $send_pass, PDO::PARAM_INT);
		$result->bindParam(':hidden_price', $hidden_price, PDO::PARAM_INT);
        
        $result->bindParam(':product_comiss', $product_comiss, PDO::PARAM_INT);
        $result->bindParam(':type_id', $product_type, PDO::PARAM_STR);

        $result->bindParam(':button_text', $button_text, PDO::PARAM_STR);
        $result->bindParam(':custom_code', $custom_code, PDO::PARAM_STR);
        $result->bindParam(':auto_add', $auto_add, PDO::PARAM_STR);
        
        $result->bindParam(':complect_params', $complect_params, PDO::PARAM_STR);
        $result->bindParam(':price_layout', $price_layout, PDO::PARAM_INT);
        $result->bindParam(':complect_sort', $complect_sort, PDO::PARAM_INT);
        $result->bindParam(':show_reviews', $show_reviews, PDO::PARAM_INT);
        $result->bindParam(':sell_once', $sell_once, PDO::PARAM_INT);
        $result->bindParam(':run_aff', $run_aff, PDO::PARAM_INT);
        
        $result->bindParam(':link', $link, PDO::PARAM_STR);
        $result->bindParam(':product_amt', $amt, PDO::PARAM_INT);
        $result->bindParam(':show_amt', $show_amt, PDO::PARAM_INT);
        $result->bindParam(':letter', $letter, PDO::PARAM_STR);
		$result->bindParam(':subject_letter', $subject_letter, PDO::PARAM_STR);
        $result->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
        $result->bindParam(':product_desc', $desc, PDO::PARAM_STR);
        $result->bindParam(':product_text1', $text1, PDO::PARAM_STR);
        $result->bindParam(':product_text2', $text2, PDO::PARAM_STR);
         $result->bindParam(':text1_tmpl', $text1_tmpl, PDO::PARAM_INT);
        $result->bindParam(':text2_tmpl', $text2_tmpl, PDO::PARAM_INT);
        
        $result->bindParam(':text1_head', $text1_head, PDO::PARAM_STR);
        $result->bindParam(':text2_head', $text2_head, PDO::PARAM_STR);
        $result->bindParam(':text1_bottom', $text1_bottom, PDO::PARAM_STR);
        $result->bindParam(':text2_bottom', $text2_bottom, PDO::PARAM_STR);
        
        $result->bindParam(':price', $price, PDO::PARAM_INT);
        $result->bindParam(':price_minmax', $price_minmax, PDO::PARAM_STR);
        $result->bindParam(':red_price', $red_price, PDO::PARAM_INT);
        $result->bindParam(':in_catalog', $in_catalog, PDO::PARAM_INT);
        $result->bindParam(':in_partner', $in_partner, PDO::PARAM_INT);
        $result->bindParam(':group_id', $add_group, PDO::PARAM_STR);
        $result->bindParam(':del_group_id', $del_group, PDO::PARAM_STR);
        $result->bindParam(':delivery_sub', $delivery, PDO::PARAM_STR);
        $result->bindParam(':delivery_unsub', $delivery_unsub, PDO::PARAM_STR);
        
        $result->bindParam(':notif_url', $notif_url, PDO::PARAM_STR);
        $result->bindParam(':author1', $author1, PDO::PARAM_INT);
        $result->bindParam(':author2', $author2, PDO::PARAM_INT);
        $result->bindParam(':author3', $author3, PDO::PARAM_INT);
        
        $result->bindParam(':comiss1', $comiss1, PDO::PARAM_INT);
        $result->bindParam(':comiss2', $comiss2, PDO::PARAM_INT);
        $result->bindParam(':comiss3', $comiss3, PDO::PARAM_INT);
        
        $result->bindParam(':type_comiss1', $type_comiss1, PDO::PARAM_STR);
        $result->bindParam(':type_comiss2', $type_comiss2, PDO::PARAM_STR);
        $result->bindParam(':type_comiss3', $type_comiss3, PDO::PARAM_STR);
        
        $result->bindParam(':subscription_id', $subscription, PDO::PARAM_INT);
        $result->bindParam(':meta_desc', $meta_desc, PDO::PARAM_STR);
        $result->bindParam(':meta_keys', $meta_keys, PDO::PARAM_STR);
        
        $result->bindParam(':base_id', $base_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        
        $result->bindParam(':pincodes', $pincodes, PDO::PARAM_STR);
        $result->bindParam(':upsell_1', $upsell_1, PDO::PARAM_INT);
        $result->bindParam(':upsell_2', $upsell_2, PDO::PARAM_INT);
        $result->bindParam(':upsell_3', $upsell_3, PDO::PARAM_INT);
        
        $result->bindParam(':upsell_1_price', $upsell_1_price, PDO::PARAM_INT);
        $result->bindParam(':upsell_2_price', $upsell_2_price, PDO::PARAM_INT);
        $result->bindParam(':upsell_3_price', $upsell_3_price, PDO::PARAM_INT);
        
        $result->bindParam(':upsell_1_desc', $upsell_1_desc, PDO::PARAM_STR);
        $result->bindParam(':upsell_2_desc', $upsell_2_desc, PDO::PARAM_STR);
        $result->bindParam(':upsell_3_desc', $upsell_3_desc, PDO::PARAM_STR);
        $result->bindParam(':upsell_1_text', $upsell_1_text, PDO::PARAM_STR);
        $result->bindParam(':upsell_2_text', $upsell_2_text, PDO::PARAM_STR);
        $result->bindParam(':upsell_3_text', $upsell_3_text, PDO::PARAM_STR);
        $result->bindParam(':ads', $zip, PDO::PARAM_STR);
        
        $result->bindParam(':text1_heading', $text1_heading, PDO::PARAM_INT);
        $result->bindParam(':text2_heading', $text2_heading, PDO::PARAM_INT);
        $result->bindParam(':show_price_box', $show_price_box, PDO::PARAM_INT);
        $result->bindParam(':code_price_box', $code_price_box, PDO::PARAM_STR);
        $result->bindParam(':to_resale', $to_resale, PDO::PARAM_INT);
        $result->bindParam(':select_payments', $select_payments, PDO::PARAM_STR);
        
        return $result->execute();
    }

      /**
     * ПОЛНОЕ КОПИРОВАНИЕ ПРОДУКТА 
     * @param $product_id
     * @return bool
     */
    public static function CopyProduct($product_id)
    {
        $db = Db::getConnection();
        $fields = self::getFields(['product_id', 'product_name', 'product_alias', 'status']);
        $extensions = [
            'product_name' => "CONCAT(product_name, ' копия')",
            'product_alias' => "CONCAT(product_alias, '-1')",
            'status' => 0,
        ];
        $sql = Db::getInsertSQL($fields, PREFICS.'products', 2, $extensions, "product_id = $product_id");

        $new_product_id = $db->query($sql) ? $db->lastInsertId() : null;
        if ($new_product_id) {
            /// Запрос копирования корзины
            $result = $db->query("INSERT INTO ".PREFICS."products_related (base_id, product_id, price, offer_desc, show_complect, status, sort)
                SELECT $new_product_id, product_id, price, offer_desc, show_complect, status, sort 
                FROM ".PREFICS."products_related WHERE base_id = $product_id");

            // Запрос копирования промокодов
            $result = $db->query("INSERT INTO ".PREFICS."products_promo (product_id, duration, promo_word, type_discount, discount, products, status, promo_desc) 
                SELECT $new_product_id, duration, promo_word, type_discount, discount, products, status, promo_desc
                FROM ".PREFICS."products_promo WHERE product_id = $product_id");

            // Запрос копирования http notice
            $result = $db->query("INSERT INTO ".PREFICS."products_http_notices (product_id, notice_name, notice_url, send_type, vars,
                send_time_type, is_send_utm)
                SELECT $new_product_id, notice_name, notice_url, send_type, vars, send_time_type, is_send_utm
                FROM ".PREFICS."products_http_notices WHERE product_id = $product_id");

            // Запрос копирования действий
            $result = $db->query("INSERT INTO ".PREFICS."product_install_act (product_id, actions ) 
                SELECT $new_product_id, actions
                FROM ".PREFICS."product_install_act WHERE product_id = $product_id");

            // Запрос копирования рассрочек
            $result = $db->query("INSERT INTO ".PREFICS."installments_to_products (product_id, installment_id) 
                SELECT $new_product_id, installment_id
                FROM ".PREFICS."installments_to_products WHERE product_id = $product_id");

            // Запрос копирования напоминалок
            $result = $db->query("INSERT INTO ".PREFICS."products_reminders (product_id, status, remind_letter1, remind_letter2, remind_letter3, remind_sms1, remind_sms2)
                SELECT $new_product_id, status, remind_letter1, remind_letter2, remind_letter3, remind_sms1, remind_sms2
                FROM ".PREFICS."products_reminders WHERE product_id = $product_id"
            );
        }

        return $new_product_id;
    }
    
    
    
    // ПЕРЕЗАПИСАТЬ ПИНКОДЫ ПОСЛЕ ОТПРАВКИ
    public static function UpdatePincodes($id, $str, $pin_count, $admin_email){
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'products SET pincodes = :pincodes WHERE product_id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':pincodes', $str, PDO::PARAM_STR);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $upd = $result->execute();
        
        // Если осталось меньше 5, то отправить письмо админу
        if($pin_count < 6){
            Email::AdminPincodeNotification($admin_email, $id, $pin_count);
        }
        
        if($upd) return true;
        else return false;
    }
    
    
    // УДАЛИТЬ ПРОДУКТ
    public static function deleteProduct($id)
    {
        $db = Db::getConnection();        
        $result = $db->query(" SELECT product_cover FROM ".PREFICS."products WHERE product_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data['product_cover'])){
            $path = ROOT .'/images/product/'.$data['product_cover'];
            if(file_exists($path)){
                unlink ($path);
            }
        }
        
        $sql = 'DELETE FROM '.PREFICS.'products WHERE product_id = :id;';
        $sql.= 'DELETE FROM '.PREFICS.'products_related WHERE product_id = :id;';
        $sql.= 'DELETE FROM '.PREFICS.'product_install_act WHERE product_id = :id;';
        $sql.= 'DELETE FROM '.PREFICS.'products_promo WHERE product_id = :id;';
        $sql.= 'DELETE FROM '.PREFICS.'products_http_notices WHERE product_id = :id;';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }
    
    
    // Получить данные типа продукта
    public static function getTypeName($id)
    {
        $db = Db::getConnection();
        $id = intval($id);
        $result = $db->query(" SELECT * FROM ".PREFICS."product_types WHERE type_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
    }
    
    /**
     *  КАТЕГОРИИ
     */
     
    // ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ
    public static function getCatData($id)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT * FROM ".PREFICS."product_category WHERE cat_id = $id ");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data;
    }
    
    
    // ПОЛУЧИТЬ ДАННЫЕ КАТЕГОРИИ по Алиасу
    public static function getCatDataByAlias($alias)
    {
        $db = Db::getConnection();
        $result = $db->query(" SELECT cat_id FROM ".PREFICS."product_category WHERE cat_alias = '$alias'");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if(isset($data) && !empty($data)) return $data['cat_id'];
    }


    /**
     * СПИСОК КАТЕГОРИЙ
     * @return array|bool
     */
    public static function getAllCatList()
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT cat_id, cat_name, cat_alias FROM ".PREFICS."product_category ORDER BY cat_id ASC");

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }
    
    
    // ДОБАВИТЬ КАТЕГОРИЮ
    public static function AddCategory($cat_name, $cat_desc, $alias, $title, $cat_keys, $cat_meta)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'product_category (cat_name, cat_alias, cat_title, cat_desc, cat_keys, cat_meta_desc ) 
                VALUES (:cat_name, :cat_alias, :cat_title, :cat_desc, :cat_keys, :cat_meta_desc)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':cat_name', $cat_name, PDO::PARAM_STR);
        $result->bindParam(':cat_title', $title, PDO::PARAM_STR);
        $result->bindParam(':cat_alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':cat_keys', $cat_keys, PDO::PARAM_STR);
        $result->bindParam(':cat_meta_desc', $cat_meta, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // ИЗМЕНИТЬ КАТЕГОРИЮ
    public static function EditCategory($id, $cat_name, $cat_desc, $alias, $title, $cat_keys, $cat_meta)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'product_category SET cat_name = :cat_name, cat_alias = :cat_alias, cat_title = :cat_title, 
        cat_desc = :cat_desc, cat_keys = :cat_keys, cat_meta_desc = :cat_meta_desc WHERE cat_id = '.$id;
        $result = $db->prepare($sql);
        $result->bindParam(':cat_name', $cat_name, PDO::PARAM_STR);
        $result->bindParam(':cat_title', $title, PDO::PARAM_STR);
        $result->bindParam(':cat_alias', $alias, PDO::PARAM_STR);
        $result->bindParam(':cat_desc', $cat_desc, PDO::PARAM_STR);
        $result->bindParam(':cat_keys', $cat_keys, PDO::PARAM_STR);
        $result->bindParam(':cat_meta_desc', $cat_meta, PDO::PARAM_STR);
        return $result->execute();
    }
    
    
    // УДАЛИТЬ КАТЕГОРИЮ с ПРОВЕРКОЙ НАЛИЧИЯ В НЕЙ ТОВАРОВ
    public static function deleteCategory($id)
    {
        $db = Db::getConnection();
        
        $result = $db->query("SELECT COUNT(product_id) FROM ".PREFICS."products WHERE cat_id = $id");
        $count = $result->fetch();
        if($count[0] == 0){
            $sql = 'DELETE FROM '.PREFICS.'product_category WHERE cat_id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            return $result->execute();   
        } else return false;
    }
    

    // ВЫВЕСТИ ТИПЫ/ТИП ПРОДУКТА
    public static function getTypes($key = null)
    {
        $prod_types = [
            1 => 'Цифровой товар',
            2 => 'Физический товар',
            3 => 'Мембершип',
        ];

        return $key ? $prod_types[$key] : $prod_types;
    }
}