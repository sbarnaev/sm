<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>Номер кошелька:</label>
            <input type="text" name="params[purse_number]" value="<?=$params['purse_number'];?>"></p>
        <p><label>Секретный код</label>
            <input type="text" name="params[secret_code]" value="<?=$params['secret_code'];?>"></p>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232370"><i class="icon-info"></i>Справка по расширению</a>
</div>