<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
  <div class="col-1-2">
    <h4 class="h4-border">Параметры</h4>
<p><label>Shop_ID</label>
<input type="text" name="params[ya_shop_id]" value="<?php echo $params['ya_shop_id'];?>"></p>

<p><label>scid:</label>
<input type="text" name="params[ya_scid]" value="<?php echo $params['ya_scid'];?>"></p>

<p><label>Пароль:</label>
<input type="text" name="params[pass]" value="<?php echo $params['pass'];?>"></p>
</div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232148"><i class="icon-info"></i>Справка по расширению</a>
</div>