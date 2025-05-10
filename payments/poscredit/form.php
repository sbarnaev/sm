<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? ' target="_blank"'.$metriks : '';

$params = unserialize(base64_decode($payment['params']));
$inv_desc = 'Оплата заказа №'.$order['order_date'];
$cl_phone = $order['client_phone'] ? str_replace('+7', '', $order['client_phone']) : '';
$product_list = [];
foreach ($order_items as $item) {
    $product = Product::getProductData($item['product_id']);
    $category = $product['cat_id'] ? Product::getCatData($product['cat_id']) : null;
    $category_name = $category ? $category['cat_name'] : '';

    if (!$category_name) {
        if ($product['type_id'] == 1) {
            $category_name = 'Цифровой товар';
        } else {
            $category_name = $product['type_id'] == 2 ? 'Физический товар' : 'Мембершип';
        }
    }

    $product_list[] = [
        'id' => $item['product_id'],
        'name' => $item['product_name'],
        'category' => $category_name,
        'price' => number_format($item['price'], 2, '.', ''),
        'count' => 1,
    ];
};
$product_list = json_encode($product_list, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK|JSON_PRESERVE_ZERO_FRACTION);?>

<script src="//api.b2pos.ru/shop/v2/connect.js" charset="utf-8" type="text/javascript"></script>
<script>
  function poscreditCheckStatus(client_status) {
    $.ajax({
      url: '/payments/poscredit/application.php',
      type: "POST",
      dataType: 'json',
      data: {order_id: '<?=$order['order_id'];?>', client_status: client_status},
      success: function ($res) {
        if ($res['status'] && (status == 4 || status == 6)) {
          window.location = '/order-info/<?=$order['order_date'];?>?client_email=<?=$order['client_email'];?>';
        }
      }
    });
  }

  function poscreditSaveProfile(profile_id) {
    $.ajax({
      url: '/payments/poscredit/application.php',
      type: "POST",
      dataType: 'json',
      data: {order_id: '<?=$order['order_id'];?>', profile_id: profile_id}
    });
  }

  function issueApplicationPosCreditOpen() {
    let cl_phone = '<?=$cl_phone;?>';
    if (!cl_phone) {
      cl_phone = prompt('Введите свой номер телефона (в формате 9XXYYYZZDD)');
      if (!cl_phone) {
        return false;
      }
    }

    poscreditServices('creditProcess', '<?=$params['access_id'];?>', {
        order: <?=$order['order_id'];?>,
        products: <?=$product_list;?>,
        phone: cl_phone,
        email: '<?=$order['client_email'];?>',
        creditType: 2
      },
      function(result) {
        if (result.success === false) {
          alert('Произошла ошибка при попытке оформить кредит. Попробуйте позднее.');
        }
      }
    );
  }
</script>

<input type="button" class="payment_btn" value="Оформить в рассрочку" onclick="issueApplicationPosCreditOpen()"/>