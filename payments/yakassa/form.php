<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));
$shop_id = $params['ya_shop_id'];
$scid = $params['ya_scid'];
?>


<form enctype="application/x-www-form-urlencoded" action="https://yoomoney.ru/eshop.xml" method="POST"<?=$form_parameters;?>>
	<input name="shopId" value="<?php echo $shop_id; ?>" type="hidden">
	<input name="scid" value="<?php echo $scid; ?>" type="hidden">
	<input name="sum" value="<?php echo $total;?>" type="hidden">
	<input name="customerNumber" value="<?php echo $order['order_date'];?>" type="hidden">
	<input name="paymentType" value="" type="hidden">
	<input name="bm_CEM" value="<?php echo $order['client_email'];?>" type="hidden">
	<input name="orderNumber" value="<?php echo $order['order_id'];?>" type="hidden">
	<input name="cps_phone" value="<?php echo $order['client_phone'];?>" type="hidden">
	<input name="cps_email" value="<?php echo $order['client_email'];?>" type="hidden">
	<input type="submit" class="payment_btn" value="Оплатить">
</form>