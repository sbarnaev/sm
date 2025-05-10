<?php defined('BILLINGMASTER') or die;

class Price {
    
    // РАСЧИТЫВАЕТ СТОИОМОСТЬ ПРОДУКТА с Учётом всех акций
    public static function getPriceinCatalog($id)
    {
        $db = Db::getConnection();
        $data = array();
        
        // Получаем цену продукта
        $result = $db->query("SELECT price, red_price FROM ".PREFICS."products WHERE product_id = $id");
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($data)) {
            $data['real_price'] = $data['price'];
            
            if (isset($_SESSION['promo_code'])) {
                $promo_sales = Product::getSaleList(1, [2,9]); // Получить список акций с типом Промо код
                
                if ($promo_sales) {
                    foreach($promo_sales as $sale) {
                        $products = unserialize($sale['products']);
                        
                        if (!empty($products) && in_array($id, $products) && $_SESSION['promo_code'] == $sale['promo_code']) {
                            $price_to_calc = $sale['promo_calc_discount'] == 2 && $data['red_price'] ? $data['red_price'] : $data['price'];
                            
                            $data['real_price'] = $sale['discount_type'] == 'summ' ? $price_to_calc - $sale['discount']
                                : $price_to_calc - ($price_to_calc / 100) * $sale['discount'];
    
                            return $data;
                        }
                    }
                }
            }
            
            if (!empty($data['red_price'])) {
                $red_sales = Product::getSaleList(1, [1]); // Получить список акций с типом Красная цена
    
                if ($red_sales) {
                    foreach ($red_sales as $sale) {
                        $products = unserialize($sale['products']);
            
                        if (!empty($products) && in_array($id, $products)) {
                            $data['real_price'] = $data['red_price'];
                            break;
                        }
                    }
                }
            }
            
            return $data;
        }
    
        return false;
    }
    
    
    // ПОЛУЧАЕТ КОНЕЧНУЮ СТОИМОСТЬ ПРОДУКТА
    public static function getFinalPrice($id, $get_partner_id = 1)
    {
        $db = Db::getConnection();
        $data = array();
        
        $setting = System::getSetting();
        $cookie = $setting['cookie'];
        
        // Получаем цену продукта
        $result = $db->query("SELECT price, red_price FROM ".PREFICS."products WHERE product_id = $id");
        
        $data = $result->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($data)) {
            $data['real_price'] = $data['price'];
            $data['sale_id'] = null;
            $data['partner_id'] = null;
    
            if (isset($_SESSION['promo_code'])) {
                $promo_sales = Product::getSaleList(1, [2,9]); // Получить список акций с типом Промо код
                
                if ($promo_sales) {
                    foreach ($promo_sales as $sale) {
                        $products = unserialize($sale['products']);
                        
                        if (!empty($products) && in_array($id, $products)) {
                            $data['sale_id'] = $sale['id'];
                            
                            if ($sale['partner_id'] != 0) {
                                $_SESSION["real_aff_$cookie"] = $sale['partner_id'];
                            }
            
                            if ($_SESSION['promo_code'] == $sale['promo_code']) {
                                $price_to_calc = $sale['promo_calc_discount'] == 2 && $data['red_price'] ? $data['red_price'] : $data['price'];
                                
                                $data['real_price'] = $sale['discount_type'] == 'summ' ? $price_to_calc - $sale['discount']
                                    : $price_to_calc - ($price_to_calc / 100) * $sale['discount'];
                                
                                return $data;
                            }
                        }
                    }
                }
            }
    
            if (!empty($data['red_price'])) {
                $red_sales = Product::getSaleList(1, [1]); // Получить список акций с типом Красная цена
    
                if ($red_sales) {
                    foreach ($red_sales as $sale) {
                        $products = unserialize($sale['products']);
            
                        if (!empty($products) && in_array($id, $products)) {
                            $data['real_price'] = $data['red_price'];
                            $data['sale_id'] = $sale['id'];
                            break;
                        }
                    }
                }
            }
        
            return $data;
        }
        
        return false;
    }
}