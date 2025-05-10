<?php define('BILLINGMASTER', 1);

$method_imgs = array(
    'yoo_money' => '/payments/yakassapi/img/yoo-money.png',
    'bank_card' => '/payments/yakassapi/img/bank_card.png',
    'sberbank' => '/payments/yakassapi/img/sberbank.png',
    'cash' => '/payments/yakassapi/img/cash.png',
    'mobile_balance' => '/payments/yakassapi/img/mobile-bal.png',
    'apple_pay' => '/payments/yakassapi/img/yakassapi.png',
    'google_pay' => '/payments/yakassapi/img/yakassapi.png',
    'qiwi' => '/payments/yakassapi/img/qiwi.png',
    'webmoney' => '/payments/yakassapi/img/webmoney.png',
    'alfabank' => '/payments/yakassapi/img/alfabank.png',
    'b2b_sberbank' => '/payments/yakassapi/img/b2b_sberbank.png',
    'tinkoff_bank' => '/payments/yakassapi/img/tinkoff.png',
    'psb' => '/payments/yakassapi/img/psb.png',
    'wechat' => '/payments/yakassapi/img/wechat.png',
);

$pay_methods = array();
if (!empty($params['pay_methods'])) {
    foreach ($method_imgs as $key => $img) {
        if (in_array($key, $params['pay_methods'])) {
            $pay_methods[$key]['desc'] = System::Lang(strtoupper("{$payment['name']}_{$key}_PAYMENT_DESC"));
            $pay_methods[$key]['title'] = System::Lang(strtoupper("{$payment['name']}_{$key}_PAYMENT_TITLE"));
            $pay_methods[$key]['img'] = $img;
        }
    }
}
?>

<?php if (!empty($params['pay_methods'])):?>
<script>
  $(function() {
    let pay_methods = <?php echo json_encode($pay_methods);?>;
    $('#yakassapi_form').parents('.order_item').hide();
    Object.entries(pay_methods).forEach(([key, data]) => {
      let block =
        '<div class="order_item">\n' +
        '  <div class="order_item-left">\n' +
        '    <img src="' + data.img + '" alt="">\n' +
        '  </div>\n' +
        '  <div class="order_item-desc">\n' +
        '    <h4>' + data.title + '</h4>\n' +
        '    <p>' + data.desc + '</p>\n' +
        '  </div>\n' +
        '  <div class="payment_button">\n' +
        '    <a href="javascript:void(0);" class="payment_btn yakassapi_btn" data-pay_method="' + key + '">Оплатить</a>\n' +
        '  </div>\n' +
        '</div>';
      $('#yakassapi_form').parents('.order_item').after(block);
    });
    $('.yakassapi_btn').click(function() {
      $('#yakassapi_form').children('input[name="pay_method"]').val($(this).data('pay_method'));
      $('#yakassapi_form').submit();
    });
  });
</script>
<?php endif;?>