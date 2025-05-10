<?php define('BILLINGMASTER', 1);

// Настройки системы
require_once(dirname(__FILE__) . '/../../components/db.php');
require_once(dirname(__FILE__) . '/../../config/config.php');

$root = dirname(__FILE__) . '/../../';
define('ROOT', $root);
define("PREFICS", $prefics);

require_once (ROOT . '/components/autoload.php');


// настройки Робокассы
$payment_name = 'robokassa';
$robox = Order::getPaymentSetting($payment_name);
$params = unserialize(base64_decode($robox['params']));
$mrh_pass2 = $params['pass2'];

//установка текущего времени
$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

// чтение параметров
$out_summ = $_REQUEST["OutSum"];
$inv_id = intval($_REQUEST["InvId"]);
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

// Данные заказа
$order = Order::getOrderDataByID($inv_id, 100);
if ($order) {
    $order_items = Order::getOrderItems($order['order_id']);
} else {
    exit('Не удалось получить данные заказа');
}
            
$total = 0; 
foreach($order_items as $item){
    $total = $total + $item['price'];
}

$summ = explode(".", $out_summ);
if ($summ[0] != $total) {
    exit('Wrong summ');
}

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

// Запись в лог
$subscription_id = null;
$query = "";
foreach($_REQUEST as $key => $value) {
    $query .= "&".$key."=".$value;
}

// проверка корректности подписи
if ($my_crc !=$crc) {
  echo "bad sign\n";
  //Email::SendMessageToBlank('joomlatown@yandex.ru', 'Oleg', 'WRONG crc!', 'Неверная подпись');
  $log = Order::writePayLog($order['order_date'], $subscription_id, 'Bad Sign', $query, $robox['payment_id']);
  exit();
}

$log = Order::writePayLog($order['order_date'], $subscription_id, 'Pay', $query, $robox['payment_id']);

// признак успешно проведенной операции
echo "OK$inv_id\n";
//Email::SendMessageToBlank('joomlatown@yandex.ru', 'Oleg', 'WRONG crc!', "OK$inv_id");

// Обработка заказа
$render = Order::renderOrder($order, $robox['payment_id']);
?>