<?php define('BILLINGMASTER', 1);

// Настройки
require_once(dirname(__FILE__) . '/../../components/db.php');
require_once(dirname(__FILE__) . '/../../config/config.php');

$root = dirname(__FILE__) . '/../../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');


$setting = System::getSetting();
$cookie = $setting['cookie'];
$status = false;

$payment_name = 'yakassapi';
$yakassa = Order::getPaymentSetting($payment_name);
$params = unserialize(base64_decode($yakassa['params']));

require (ROOT .'/lib/yakassa_sdk/autoload.php');
use YandexCheckout\Client;//Импортируем класс

$client = new Client();//Создаём экземпляр объекта
$client->setAuth($params['ya_shop_id'], $params['api_key']);

$json = file_get_contents('php://input');
$payment = json_decode($json, true);

$paymentId = $payment ? $payment['object']['id'] : false;


if ($paymentId) {
  try {
    $payment = $client->getPaymentInfo($paymentId);//$paymentId);
	//Если на метод передать некорректный Id платежа, то обработка скрипта остановится, чтобы этого не произошло 
	//обязательно обрабатываем ситуацию с исключением по try - catch
  } catch (Exception $e) {}
}

if (isset($payment->status) and (($payment->status == "succeeded") )) {

    // Получаем данные заказа
    $order = Order::getOrderToAdmin(intval($payment->metadata->order_id));
    if(!$order) exit('order not found');
    
    $summ = Order::getOrderTotalSum($payment->metadata->order_id);
    
    // Проверяем данные заказа с пришедшими
    $amount = $payment->amount->value;
    if($amount == $summ.'.00' && $order['status'] != 1) {   
        
        // Рендерим заказ
  		$render = Order::renderOrder($order, $yakassa['payment_id']);
       
    }
}


if(isset($order)){
    ob_start();
    echo '<pre>';
    print_r($payment);
    echo '</pre>';
    $buffer = ob_get_contents();
    $log = Order::writePayLog($order['order_date'], null, 'All', $buffer, $yakassa['payment_id']);
    ob_end_clean();
}

?>

<html>
<head>
<title>Успешная оплата</title>
</head>
<body>
<div class="login-userbox">
<h1>Спасибо за оплаченный заказ!</h1>
<p>Дальнейшие инструкции высланы на ваш e-mail адрес.<br />На всякий случай проверьте папку СПАМ.</p>
<p>Вернуться на <a href="/">главную страницу</a></a></p>
</div>
<style>
  @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i&display=swap&subset=cyrillic');
  body{
    background: rgba(240, 243, 255, 0.7);
    margin: 0;
    padding: 20px;
    width: 100%;
    box-sizing: border-box;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Open Sans', sans-serif;
    -webkit-text-size-adjust: 100%;
    -ms-text-size-adjust: 100%;
    color: #000;
    font-size: 16px;
  }
  .login-userbox {
    background: #fff;
    border-radius: 10px;
    max-width: 780px;
    width: 100%;
    padding: 50px 60px 60px;
    margin: auto;
    margin-top: 90px;
  }
</style>
</body>
</html>