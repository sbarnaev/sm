<?php define('BILLINGMASTER', 1);

if (!empty($_POST)) {

    // Настройки системы
    require_once(dirname(__FILE__) . '/../../components/db.php');
    require_once(dirname(__FILE__) . '/../../config/config.php');

    $root = dirname(__FILE__) . '/../../';
    define('ROOT', $root);
    define("PREFICS", $prefics);

    require_once (ROOT . '/components/autoload.php');


    $setting = System::getSetting();
    $total = 0;
    
    // Получаем заголовки запроса
    $headers = getallheaders();
    
    // настройки CloudPayments
    $payment_name = 'cloudpayments';
    $cloudpayments = Order::getPaymentSetting($payment_name);
    $params = unserialize(base64_decode($cloudpayments['params']));
    $api_pass = $params['pass_api'];
    
    // Формируем подпись для проверки подлинности запроса
    $body = file_get_contents('php://input');
    $sign = hash_hmac('sha256', $body, $api_pass, true);
    $sign = base64_encode($sign);  

	$hmac = '8';
	
	$query = "";
    foreach($_POST as $key => $value) {
        $query .= "&".$key."=".$value;
    }
    
    if(isset($headers['Content-Hmac'])) $hmac = $headers['Content-Hmac'];
    elseif(isset($headers['Content-HMAC'])) $hmac = $headers['Content-HMAC'];

    if ($hmac != $sign) exit;
    
    $subscription_id = isset($_POST['SubscriptionId']) ? htmlentities($_POST['SubscriptionId']) : null;
	$order_date = isset($_POST['InvoiceId']) ? intval($_POST['InvoiceId']) : 0;

    // CHECK запрос 
    if (isset($_GET['check'])) {
        
		$log = Order::writePayLog($order_date, $subscription_id, 'Check', $query, $cloudpayments['payment_id']);
		
		// Если это ещё не оплаченный заказ, проверяем сумму
        if (isset($_POST['InvoiceId']) && !empty($_POST['InvoiceId'])) {
            $order_data = Order::getOrderData($order_date);

            if ($order_data['status'] == 0 || $order_data['status'] == 5) {
                if ($order_data['installment_map_id'] == 0) {
                    $order_items = Order::getOrderItems($order_data['order_id']);
                    foreach($order_items as $item) {
                        $total = $total + $item['price'];
                    }
                    
                    $summ = $total.'.00';
                    
                    echo $summ == $_POST['Amount'] ? '{"code":0}' : '{"code":12}';
                } else {
                    $summ = $order_data['summ'].'.00';

                    echo $summ == $_POST['Amount'] ? '{"code":0}' : '{"code":12}';
                }
            }
        } elseif (isset($_POST['SubscriptionId'])) {
            echo '{"code":0}';
        }
    }
    
    
    // PAY запрос 
    if (isset($_GET['pay'])) {
        
		$log = Order::writePayLog($order_date, $subscription_id, 'Pay', $query, $cloudpayments['payment_id']);
		if (isset($_POST['InvoiceId']) && !empty($_POST['InvoiceId'])) { // InvoiceId приходит когда оплачивается новый заказ
       
            $order_data = Order::getOrderData($order_date);

            // Если заказ не оплаченый
            if ($order_data['status'] == 0 || $order_data['status'] == 5) {
                
                echo '{"code":0}';
                // Обработка заказа
          		$render = Order::renderOrder($order_data, $cloudpayments['payment_id'], $subscription_id);
            }
            
        } else {
            if (isset($_POST['SubscriptionId'])) { // При продлении InvoiceId не приходит
                $email = isset($_POST['Email']) ? htmlentities($_POST['Email']) : 'check-cloudpayments@bm.ru';
                $name = isset($_POST['Name']) ? htmlentities($_POST['Name']) : 'Имя не указано?';
                
                $summ = $_POST['Amount'];
                $ip = $_POST['IpAddress'];
 
                // продлить подписку по рекуррентам
                $prolong = Member::MemberProlong($subscription_id, $email, $name, $summ, $ip);

                echo $prolong ? '{"code":0}' : '{"code":1}';
            
            } else {
                $subscription_id = null;
            }
        }
        
    }
    
    
    // ОТ ОНЛАЙН КАССЫ
    if (isset($_GET['receipt'])) {
        
        if (isset($_POST['InvoiceId']))  echo '{"code":0}';
        
    }
    
    
    // Recurrent
    if (isset($_GET['recurrent'])) {
        
        // Отмена подписки со страницы Cloudpayments
        if (isset($_POST['Status']) && $_POST['Status'] == 'Cancelled' ) {
            
            $subs_id = htmlentities($_POST['Id']);
            
            //Отменить подписку в BM по subscription_id
            /*
            $search = Member::getSubscriptionRecurrent($subs_id);
            if ($search) {
                $act = Member::pauseMember($search['id'], 0);
            }
            */
            
        }
        
        echo '{"code":0}';
        
    }
}