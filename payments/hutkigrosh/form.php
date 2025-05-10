<?php defined('BILLINGMASTER') or die;

$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('GO_PAY');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'go_pay', 'submit');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onsubmit="'.$ya_goal.$ga_goal.' return true;"';

$params = unserialize(base64_decode($payment['params']));
$inv_desc = 'Оплата заказа №'.$order['order_date'];?>

<form id="hk_form" action="/payments/hutkigrosh/pay.php" method="POST"<?=$metriks;?>>
    <input type="hidden" name="order_id" value="<?=$order['order_id'];?>">
    <input type="hidden" name="client_name" value="<?=$order['client_name'];?>">
    <input type="hidden" name="client_email" value="<?=$order['client_email'];?>">
    <input type="hidden" name="client_phone" value="<?=$order['client_phone'];?>">
    <input type="hidden" name="client_city" value="<?=$order['client_city'];?>">
    <input type="hidden" name="client_address" value="<?=$order['client_address'];?>">
    <input type="hidden" name="ship_method_id" value="<?=$order['ship_method_id'];?>">
    <input type="hidden" name="product_id" value="<?=$order['product_id'];?>">
    <input type="hidden" name="order_desc" value="<?=$inv_desc;?>">
    <input type="button" id="hk_btn" class="payment_btn" value="Оплатить">
</form>

<div id="modal_hk" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-3">
        <div class="userbox modal-userbox-3">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close">
                <span class="icon-close"></span>
            </a>

            <h3 class="modal-head-2">Итого к оплате: <?="$total {$setting['currency']}";?></h3>
            <hr>
            <div id="result"></div>
        </div>
    </div>
</div>

<script>
    $('#hk_btn').click(function(){
        $.ajax({
            url: "/payments/hutkigrosh/pay.php",
            type: "POST",
            data: $("#hk_form").serialize(),
            success: function (html) {
              if (html.error) {
                alert(html.error);
              } else if(html !== ''){
                $("#result").html(html);
                UIkit.modal("#modal_hk").show();
              }
            }
        });
    });
</script>