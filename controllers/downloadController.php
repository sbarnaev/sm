<?php defined('BILLINGMASTER') or die; 


class downloadController {
    
    
    // Скачивание продуктов из заказа
    public function actionIndex($order_date)
    {
        if(isset($_GET['key'])){
            
            // Получить данные заказа по order_date
            $noindex = 1;
            $meta_desc = ''; 
            $meta_keys = '';
            $use_css = 1;
            $is_page = '';
            $dwl = 0; // Флаг для скачивания
            $setting = System::getSetting();
            $dwl_time = $setting['dwl_time'] * 3600;
            $dwl_count = $setting['dwl_count'];
            $now = time(); // Текущее время
            
            $order = Order::getOrderData($order_date, 1); // Получить данные заказа
            if(!$order) {
            require_once(ROOT . '/template/'.$setting['template'].'/404.php');
            exit();   
            }
            
            // Проверка ключа
            if(md5($order['client_email']) != $_GET['key']) exit('Неверные параметры ключа');
            
            // Проверка ограничения по времени
            if($order['dwl_time'] == null){
                if(($order['payment_date'] + $dwl_time) > $now ) $dwl = 1;
                // Обновляем дату начала скачивания
                $upd = Order::UpdateOrderDwl($order_date, $now);
                
            } else {
                if(($order['dwl_time'] + $dwl_time) > $now) $dwl = 1;
            }
            
            if($dwl == 1){
                
               $_SESSION["dwl_order_$order_date"] = 1;
                
               if(isset($_POST['download'])){
                    if(isset($_SESSION["dwl_order_$order_date"]) && $_SESSION["dwl_order_$order_date"] == 1){
                        
                        $item = intval($_POST['item']);
                        
                        // Проверка существования данного продукта в заказе
                        $item_data = Order::ExistProductInOrder($order['order_id'], $item);
                        if($item_data){
                            $product = Product::getProductDataForSendOrder($item);
                            //header("Content-Disposition: attachment; filename=".$product['link']);
                            // https://habr.com/post/151795/
                            header("Location: ".$product['link']);
                            
                            Order::UpdateOrderDwlCount( $item_data['order_item_id'], $item_data['dwl_count'] + 1);   
                        } else exit('Ошибка. Данного продукта нет в заказе');
                    }
                
                
               }
               
               $items = Order::getOrderItems($order['order_id']);
               require_once (ROOT . '/template/'.$setting['template'].'/views/order/paid_load.php');
                
            } else exit('Время скачивания истекло');
            
            
            
        }
}
}