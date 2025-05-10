<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));
?>
<div class="row-line">
  <div class="col-1-2">
    <h4 class="h4-border">Параметры</h4>
    <p><label>ID магазина (Merchant Account):</label>
      <input type="text" name="params[merchant]" value="<?php echo $params['merchant'];?>"></p>

    <p><label>Secretkey:</label>
      <input type="text" name="params[secretkey]" value="<?php echo $params['secretkey'];?>"></p>

    <p><label>Валюта (RUB, USD, UAH, EUR):</label>
      <input type="text" name="params[currency]" value="<?php echo $params['currency'];?>"></p>

  </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232108"><i class="icon-info"></i>Справка по расширению</a>
</div>