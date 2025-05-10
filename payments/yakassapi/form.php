<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));
$sel_pay_mtds = isset($params['sel_pay_mtds']) ? $params['sel_pay_mtds'] : '';
?>


<form enctype="application/x-www-form-urlencoded" id="yakassapi_form" action="/payments/yakassapi/pay.php" method="POST"<?=$form_parameters?>>
	<input name="sum" value="<?php echo $total;?>" type="hidden">
	<input name="customerNumber" value="<?php echo $order['order_date'];?>" type="hidden">
	<input name="bm_CEM" value="<?php echo $order['client_email'];?>" type="hidden">
	<input name="orderNumber" value="<?php echo $order['order_id'];?>" type="hidden">
	<input name="cps_phone" value="<?php echo $order['client_phone'];?>" type="hidden">
	<input name="cps_email" value="<?php echo $order['client_email'];?>" type="hidden">
    <input name="pay_method" value="" type="hidden">
	<input type="submit" name="pay" class="payment_btn" value="Оплатить">
</form>

<?php if ($sel_pay_mtds == 'this_site') {
    require_once (dirname(__FILE__) . '/pay_methods.php');
}