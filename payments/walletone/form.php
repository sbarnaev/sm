<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

// настройки walletone
$payment_name = 'walletone';
$walletone = Order::getPaymentSetting($payment_name);
$params = unserialize(base64_decode($walletone['params']));
$merchant_id = trim($params['merchant_id']);
$secret_key = trim($params['secret_key']);
$inv_desc = 'BASE64:' . base64_encode("Оплата заказа №{$order['order_date']}");
$amount = number_format($total, 2, '.', '');
$account_time = $params['account_time'] > 0 && $params['account_time'] < 31 ? $params['account_time'] : 30;
$expired_date = gmdate("Y-m-d\TH:i:s", time() + 86400 * $account_time);

$form_fields = array(
    'WMI_MERCHANT_ID' => $merchant_id,
    'WMI_PAYMENT_AMOUNT' => $amount,
    'WMI_CURRENCY_ID' => $params['currency_id'],
    'WMI_PAYMENT_NO' => "{$order['order_id']}-{$order['order_date']}",
    'WMI_DESCRIPTION' => $inv_desc,
    'WMI_CUSTOMER_EMAIL' => $order['client_email'],
    'WMI_RECIPIENT_LOGIN' => $order['client_email'],
    'WMI_EXPIRED_DATE' => $expired_date,
    'WMI_SUCCESS_URL' => $setting['script_url'] . '/payments/walletone/success.php',
    'WMI_FAIL_URL' => $setting['script_url'] . '/payments/walletone/fail.php',
);

$taxes = array(
    'without' => array('code' => 'tax_ru_1', 'formula' => false),
    '0' => array('code' => 'tax_ru_2', 'formula' => false),
    '10' => array('code' => 'tax_ru_3', 'formula' => 'round(%02.2f * 10/100, 2)'),
    '18' => array('code' => 'tax_ru_4', 'formula' => 'round(%02.2f * 18/100, 2)'),
    '10/110' => array('code' => 'tax_ru_5', 'formula' => 'round(%02.2f * 10/110, 2)'),
    '18/118' => array('code' => 'tax_ru_6', 'formula' => 'round(%02.2f * 18/118, 2)'),
    '20' => array('code' => 'tax_ru_7', 'formula' => 'round(%02.2f * 20/100, 2)'),
    '20/120' => array('code' => 'tax_ru_8', 'formula' => 'round(%02.2f * 20/120, 2)'),
);
$tax_type = $taxes[$params['tax_type']]['code'];
$formula = $taxes[$params['tax_type']]['formula'];

$receipt_items = array();
$order_items = Order::getOrderItems($order['order_id']);
if(!empty($order_items)) {
    foreach ($order_items as $order_item) {
        $tax = $formula ? eval('return '. sprintf($formula, $order_item['price']) .';') : 0;
        $receipt_items[] = array (
            'Title' => iconv("UTF-8", "windows-1251", $order_item['product_name']),
            'Quantity' => iconv("UTF-8", "windows-1251", 1),
            'UnitPrice' => iconv("UTF-8", "windows-1251", number_format($order_item['price'], 2, '.', '')),
            'SubTotal' => iconv("UTF-8", "windows-1251", number_format($order_item['price'], 2, '.', '')),
            'TaxType' => iconv("UTF-8", "windows-1251", $tax_type),
            'Tax' => iconv("UTF-8", "windows-1251", $tax),
        );
    }
};

$form_fields['WMI_ORDER_ITEMS'] = json_encode($receipt_items, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);

if (!empty($params['pt_enabled'])) {
    foreach ($params['pt_enabled'] as $value) {
        $form_fields['WMI_PTENABLED'][] = $value;
    }
}
if (!empty($params['pt_disabled'])) {
    foreach ($params['pt_disabled'] as $value) {
        $form_fields['WMI_PTDISABLED'][] = $value;
    }
}

foreach($form_fields as $name => $val) {
    if(is_array($val)) {
        usort($val, "strcasecmp");
        $form_fields[$name] = $val;
    }
}
uksort($form_fields, "strcasecmp");

$form_values = '';
foreach ($form_fields as $name => $value) {
    if (is_array($value)) {
        foreach ($value as $v) {
            $form_values .= iconv("UTF-8", "windows-1251", $v);
        }
    } else {
        $form_values .= iconv("UTF-8", "windows-1251", $value);
    }
}

$signature = base64_encode(pack("H*", md5($form_values . iconv("UTF-8", "windows-1251", $secret_key))));
$form_fields['WMI_SIGNATURE'] = $signature;
?>

<form enctype="application/x-www-form-urlencoded" method="POST" action="https://wl.walletone.com/checkout/checkout/Index"<?=$form_parameters;?>>
    <?php foreach ($form_fields as $name => $value):?>
        <?php if(is_array($value)):?>
            <?php foreach ($value as $v):
                if($v != NULL):?>
                    <input type="hidden" name="<?=$name?>" value="<?=$v;?>"/>
                <?php endif;?>
            <?php endforeach?>
        <?php else:?>
            <input type="hidden" name="<?=$name?>" value="<?=$value;?>"/>
        <?php endif?>
    <?endforeach?>
    <input type="submit" class="payment_btn" value="Оплатить">
</form>