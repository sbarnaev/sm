<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
  <div class="col-1-2">
    <h4 class="h4-border">Параметры</h4>
<p><label>Email в Paypal</label>
<input type="text" name="params[business]" value="<?php echo $params['business'];?>"></p>

<p><label>Валюта (RUB, USD и т.д.):</label>
<input type="text" name="params[currency]" value="<?php echo $params['currency'];?>"></p>
</div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232760"><i class="icon-info"></i>Справка по расширению</a>
</div>