<?php defined('BILLINGMASTER') or die;

$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'go_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';

if(isset($_SESSION['org'])) {
    $payments = json_decode($_SESSION['org']['payments'], true);
    $requisits = json_decode($_SESSION['org']['requisits'], true);
    if($payments['cloud']['enable'] == 1) {
        $cloud = 1;
    }
}

$params = unserialize(base64_decode($payment['params']));
$public_id = isset($cloud) ? $payments['cloud']['public_id'] : $params['public_id'];
$pass_api = isset($cloud) ? $payments['cloud']['api_pass'] : $params['pass_api'];
$currency = isset($cloud) ? $payments['cloud']['currency'] : $params['currency'];

$online_kassa = isset($cloud) ? $requisits['kassa'] : $params['online_kassa'];
$taxation_system = isset($cloud) ? $requisits['taxationsystem'] : $params['taxationsystem'];
$vat_code = isset($cloud) ? $requisits['nds'] : $params['vat_code'];
$org_id = isset($cloud) ? $_SESSION['org']['id'] : 0;
$object = isset($params['object']) ? $params['object'] : 1;
$show_checkbox = $get_member && $recurrent_enable && $params['checkbox'] ? true: false;

foreach ($order_items as $item) {
    $items[] = [
        "label" => $item['product_name'],
        "quantity" => "1",
        "price" => $item['price'].'.00',
        "quantity" => '1.00',
        "amount" => $item['price'].'.00',
        "vat" => $params['vat_code'],
        "method" => 'FullPay',
        "object" => $object,
        "measurementUnit" => 'шт',
    ];
}?>

<script>
  this.pay = function () {
    var widget = new cp.CloudPayments({googlePaySupport: false, applePaySupport: false});
    <?php if($online_kassa == 1):?>
        var receipt = {
          "Items": <?=json_encode($items, JSON_UNESCAPED_UNICODE);?>,
          "calculationPlace": '<?=$setting['script_url'];?>',
          "taxationSystem": <?=$taxation_system;?>,
          "email": "<?=$order['client_email']?>",
          "phone": "",
          "customerInfo": "<?=$order['client_name']?>",
          "customerInn": "",
          "isBso": false,
          "amounts": {
            "electronic": <?=$total;?>.00, // Сумма оплаты электронными деньгами
            "advancePayment": 0.00, // Сумма из предоплаты (зачетом аванса) (2 знака после запятой)
            "credit": 0.00, // Сумма постоплатой(в кредит) (2 знака после запятой)
            "provision": 0.00 // Сумма оплаты встречным предоставлением (сертификаты, др. мат.ценности) (2 знака после запятой)
          }
        };
    <?php endif;?>

    var data = {
      cloudPayments: {
        customOrg: {"customOrg": <?=$org_id;?>} //разделение финпотока
        <?php if($online_kassa == 1) echo ', customerReceipt: receipt';?>
      }
    };

    <?php if($get_member): // Если в заказе есть продукт подписка, получаем данные подписки
        $delay = $plane['delay'] != 0 ? date("Y-m-d H:i:s", $now + $plane['delay'] * 86400) : null; // Отсрочка регулярного платежа (формат startdate = 2020-03-01 23:36:49)
        $max_periods = $plane['max_periods'] > 0 ? $plane['max_periods'] : 0; // Максимальное кол-во списаний
        $amount = $plane['amount'] != null ? $plane['amount'] : false; // Сумма регулярного платежа;?>

        var recurrent = {
          interval: '<?=$plane['period_type'];?>',
          period: <?=$plane['lifetime'];?>
          <?php if($delay) echo ", StartDate: '$delay'";?>
          <?php if($max_periods > 0) echo ", maxPeriods: $max_periods";?>
          <?php if($amount) echo ", Amount: $amount";?>
          <?php if($online_kassa == 1) echo ", customerReceipt: receipt"?>
        };

        <?php if($show_checkbox):?>
            if ($('#recurrent').is(':checked')) {
              data.cloudPayments.recurrent = recurrent;
            }
        <?php elseif($recurrent_enable):?>
            data.cloudPayments.recurrent = recurrent;
        <?php endif;
    endif;?>

    widget.charge({ // options
      publicId: '<?=$public_id?>',
      description: 'Оплата заказа № <?=$order['order_date'];?>', //назначение
      amount: <?=$total;?>.00,
      currency: '<?=$currency;?>',
      invoiceId: '<?=$order['order_date'];?>',
      accountId: '<?=$order['client_email'];?>', //идентификатор плательщика (лучше емейл)
      email:'<?=$order['client_email'];?>',
      skin: "classic", //дизайн виджета
      data: data
    },
    function (options) { // success
      window.location.assign("<?=$setting['script_url'];?>/payments/cloudpayments/success.php");
    },
    function (reason, options) { // fail
      //window.location.assign("/widget/");
    });
};    

$(document).on('click', '#checkout', function (e) {
  e.preventDefault();
  pay();
});
</script>

<button class="order_button btn-green-small mb-15" id="checkout">Оплатить</button>
<?php if($show_checkbox):?>
    <label class="mb-0 check_label">
        <input type="checkbox" id="recurrent">
        <span><?=$plane['recurrent_label'];?></span>
    </label>

    <script>
      $(document).ready(function(){
        $('.order_button').prop('disabled', true);
        $('#recurrent').change(function() {
          $('.order_button').prop('disabled', function(i, val) {
            return !val;
          })
        });
      });
    </script>
<?php endif;?>