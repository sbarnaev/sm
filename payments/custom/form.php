<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('CUSTOM_PAY');" : null;
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'custom_pay', 'submit');" : null;
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : null;
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? $metriks : '';
?>

<a class="payment_btn payment_btn-link" href="#ModalCustomPay" data-uk-modal>Инструкция</a>

<div id="ModalCustomPay" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-3">
        <div class="userbox modal-userbox-3">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
            <form enctype="application/x-www-form-urlencoded" action="" method="POST"<?=$form_parameters;?>>
                <h3 class="modal-head-2">Итого к оплате: <?php echo $total; ?> <?php echo $setting['currency'];?></h3>
                <?php $params = unserialize(base64_decode($payment['params'])); echo $params['instruct'];?>
                <hr>
                <p>К оплате: <strong><?php echo $total; ?> <?php echo $setting['currency'];?></strong></p>
                <div>
                    <h5 class="one-filter__title">Выберите систему через которую оплачивали:</h5>
                    <div class="select-wrap">
                        <select name="gateway" required="required">
                            <option value="">- Выбрать -</option>
                            <?php $gateway_list = explode(",", $params['gateway']);
                                foreach($gateway_list as $gateway):?>
                            <option value="<?php echo $gateway;?>"><?php echo $gateway;?></option>
                            <?php endforeach;?>
                        </select>
                   </div>
                </div>
                <p>Укажите детали платежа для быстрого поиска оплаты (наименование банка или последние 4 цифры вашего счёта, карты, кошелька)</p>
                <input type="text" name="card_number">
                <input type="hidden" name="payment" value="<?php echo $payment['payment_id'];?>">
                <input type="hidden" name="summ" value="<?php echo $total;?>">
                <p><input type="submit" name="custom_pay" class="order_button btn-green-small mt-5" value="Оплачено"></p>
            </form>
        </div>
    </div>
</div>