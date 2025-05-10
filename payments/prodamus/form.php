<?php defined('BILLINGMASTER') or die;

require_once __DIR__ . '/Hmac.php';

$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));

$linktoform = $params['prodamus_site_name'];

// Секретный ключ. Можно найти на странице настроек, 
// в личном кабинете платежной формы.
$secret_key = $params['prodamus_secret_key'];

$tax = isset($params['tax']) ? $params['tax'] : 'none';
$payment_method = isset($params['payment_method']) ? $params['payment_method'] :'full_prepayment';
$payment_object = isset($params['payment_object']) ? $params['payment_object'] : 'commodity';
$available_payment_methods = isset($params['available_payment_methods']) ? str_replace([' ',','],['','|'],$params['available_payment_methods']) : '';

$items_prod = [];
foreach($order_items as $item){
    $items_prod[] = [
        'name' => $item['product_name'],
        'quantity' => 1,
        'price' => $item['price'] . '.00',
        'tax' => $tax,
        'paymentMethod' => $payment_method,
        'paymentObject' => $payment_object
    ];
}
$ship_method = !empty($order['ship_method_id']) ? System::getShipMethod($order['ship_method_id']) : null;
if ($ship_method) {
    $items_prod[] = [
        'name' => $ship_method['title'],
        'quantity' => 1,
        'price' => $ship_method['tax'] . '.00',
        'tax' => $tax,
        'paymentMethod' => $payment_method,
        'paymentObject' => $pay_object_delivery
    ];
}

$data = [
	// хххх - номер заказ в системе интернет-магазина
	'order_id' => $order['order_id'],

	// +7хххххххххх - мобильный телефон клиента
	'customer_phone' => !empty($order['client_phone']) ? $order['client_phone'] : '',

	// ИМЯ@prodamus.ru - e-mail адрес клиента
	'customer_email' => $order['client_email'],

	// перечень товаров заказа
	'products' => $items_prod,

	// для интернет-магазинов доступно только действие "Оплата"
	'do' => 'pay',

	// url-адрес для возврата пользователя без оплаты 
	//           (при необходимости прописать свой адрес)
	'urlReturn' => $setting['script_url'] . '/payments/prodamus/fail.php',

	// url-адрес для возврата пользователя при успешной оплате 
	//           (при необходимости прописать свой адрес)
	'urlSuccess' => $setting['script_url'] . '/payments/prodamus/success.php',

	// служебный url-адрес для уведомления интернет-магазина 
	//           о поступлении оплаты по заказу
	// 	         пока реализован только для Advantshop, 
	//           формат данных настроен под систему интернет-магазина
	//           (при необходимости прописать свой адрес)
	'urlNotification' => '',

	// код системы интернет-магазина, запросить у поддержки, 
	//     для самописных систем можно оставлять пустым полем
	//     (при необходимости прописать свой код)
	'sys' => 'schoolmaster',

	// метод оплаты, выбранный клиентом
	// 	     если есть возможность выбора на стороне интернет-магазина,
	// 	     иначе клиент выбирает метод оплаты на стороне платежной формы
	//       варианты (при необходимости прописать значение):
	// 	AC - банковская карта
	// 	PC - Яндекс.Деньги
	// 	QW - Qiwi Wallet
	// 	WM - Webmoney
	// 	GP - платежный терминал
	'available_payment_methods' => $available_payment_methods,

	// сумма скидки на заказ
	// 	     указывается только в том случае, если скидка 
	//       не прменена к товарным позициям на стороне интернет-магазина
	// 	     алгоритм распределения скидки по товарам 
	//       настраивается на стороне пейформы
	'discount_value' => 0.00
];


$data['signature'] = Hmac::create($data, $secret_key);

$link = sprintf('%s?%s', $linktoform, http_build_query($data));
?>
<form enctype="application/x-www-form-urlencoded" action="<?=$link?>" method="POST">
    <input type="submit" class="payment_btn" value="Оплатить">
</form>