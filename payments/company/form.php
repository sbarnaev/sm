<?php defined('BILLINGMASTER') or die;
$ya_goal = !empty($setting['yacounter']) ? "yaCounter".$setting['yacounter'].".reachGoal('CUSTOM_PAY');" : '';
$ga_goal = $setting['ga_target'] == 1 ? "ga ('send', 'event', 'custom_pay', 'submit');" : '';
$metriks = $ya_goal || $ga_goal ? ' onsubmit="'.$ya_goal.$ga_goal.' return true;"' : '';
$form_parameters = strpos($_SERVER['HTTP_USER_AGENT'], 'Instagram') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'vkShare') === false ? $metriks : '';
?>

<a class="payment_btn payment_btn-link" href="#ModalCompanyPay" data-uk-modal>Выписать счёт</a>

<div id="ModalCompanyPay" class="uk-modal">
  <div class="uk-modal-dialog uk-modal-dialog-3">
    <div class="userbox modal-userbox-3">
      <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>
      <form enctype="application/x-www-form-urlencoded" action="" method="POST"<?=$form_parameters;?>>
        <h3 class="modal-head-2">Итого к оплате: <?php echo $total; ?> <?php echo $setting['currency'];?></h3>
        <?php $params = unserialize(base64_decode($payment['params'])); echo $params['instruct'];?>
        <hr>
        <p>К оплате: <strong><?php echo $total; ?> <?php echo $setting['currency'];?></strong></p>
        <div>
          <p><label>Наименование организации: <input type="text" name="organization" required="required"></label></p>

          <div class="modal-form-line"><label>ИНН/КПП:</label><input type="text" name="inn" required="required"></div>
          <div class="modal-form-line"><label>БИК:</label><input type="text" name="bik"></div>
          <div class="modal-form-line"><label>Р/счёт:</label><input type="text" name="rs" required="required"></div>

          <div class="modal-form-line"><label>Адрес для отправки закрывающих документов:</label>
            <textarea name="address" cols="35" rows="3"></textarea></div>

          <input type="hidden" name="payment" value="<?php echo $payment['payment_id'];?>">
          <input type="hidden" name="summ" value="<?php echo $total;?>">
          <div class="modal-form-submit mb-0"><input type="submit" name="company_pay" class="order_button btn-green-small" value="Выставить счёт"></div>
        </div>
      </form>
    </div>
  </div>
</div>