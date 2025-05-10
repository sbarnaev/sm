<?php define('BILLINGMASTER', 1);

if (isset($_POST['action']) && isset($_POST['orderNumber'])) {

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    // настройки Якассы
    $payment_name = 'yakassa';
    $yakassa = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($yakassa['params']));
    
    $order_id = intval($_POST['orderNumber']);
    $bm_CEM = htmlentities($_POST['bm_CEM']);
    
    // Получаем данные заказа
    $order = Order::getOrderDataByID($order_id, 100);
    
    // Получаем список продуктов в заказе
    if ($order) {
        $order_items = Order::getOrderItems($order_id);
    } else {
        exit('Не удалось получить данные заказа');
    }

    if ($order['client_email'] != $bm_CEM) {
        exit('Что-то пошло не так');
    }

    // Считаем сумму заказа
    $total = 0; 
    foreach($order_items as $item){
        $total = $total + $item['price'];
    }
    
    $summ = $total.'.00';
    
    // Проверка платежа Check Order
          
    if ($_POST['action'] == 'checkOrder') {
    	
    	$hash = md5($_POST['action'].';'.$summ.';'.$_POST['orderSumCurrencyPaycash'].';'.$_POST['orderSumBankPaycash'].';'.$params['ya_shop_id'].';'.$_POST['invoiceId'].';'.$_POST['customerNumber'].';'.$params['pass']);		
    	if (strtolower($hash) != strtolower($_POST['md5'])){
    		$code = 1;
    	}
    	else {
    		$code = 0;
    	}
    		$answer = '<?xml version="1.0" encoding="UTF-8"?>
    		<checkOrderResponse performedDatetime="'. $_POST['requestDatetime'] .'" code="'.$code.'"'. ' invoiceId="'. $_POST['invoiceId'] .'" shopId="'. $params['ya_shop_id'] .'"/>';
    		echo $answer;
            //Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'checkOrder', $answer);
    }
    
    
    
    // Уведомление об оплате
        
    if ($_POST['action'] == 'paymentAviso') {
    	
    	$hash = md5($_POST['action'].';'.$summ.';'.$_POST['orderSumCurrencyPaycash'].';'.$_POST['orderSumBankPaycash'].';'.$params['ya_shop_id'].';'.$_POST['invoiceId'].';'.$_POST['customerNumber'].';'.$params['pass']);		
    	if (strtolower($hash) != strtolower($_POST['md5'])){
    		$code = 1;
    	} else {
    		$code = 0;
    		
    		$answer = '<?xml version="1.0" encoding="UTF-8"?>
    		<paymentAvisoResponse performedDatetime="'. $_POST['requestDatetime'] .'" code="'.$code.'"'. ' invoiceId="'. $_POST['invoiceId'] .'" shopId="'. $params['ya_shop_id'] .'"/>';
    		echo $answer;
            //Email::SendMessageToBlank('report@kasyanov.info', 'Oleg', 'paymentAviso', $answer);
    		
    		// Обработка заказа
    		$render = Order::renderOrder($order, $yakassa['payment_id']);
    		
    	}
    }
}
?>