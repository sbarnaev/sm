<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));
?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>Public key:</label>
            <input type="text" name="params[public_key]" value="<?=$params['public_key'];?>"></p>
        <p><label>Private key</label>
            <input type="text" name="params[private_key]" value="<?=$params['private_key'];?>"></p>
        <div class="width-100"><label>Валюта заказа:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[currency]" type="radio" value="RUB" <?php if($params['currency'] == 'RUB') echo 'checked';?>><span>RUB</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="USD" <?php if($params['currency'] == 'USD') echo 'checked';?>><span>USD</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="EUR" <?php if($params['currency'] == 'EUR') echo 'checked';?>><span>EUR</span></label>
                <br>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="UAH" <?php if($params['currency'] == 'UAH') echo 'checked';?>><span>UAH</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="BYN" <?php if($params['currency'] == 'BYN') echo 'checked';?>><span>BYN</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="KZT" <?php if($params['currency'] == 'KZT') echo 'checked';?>><span>KZT</span></label>
            </span></div>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232758"><i class="icon-info"></i>Справка по расширению</a>
</div>