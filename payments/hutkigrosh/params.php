<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>ЕРИП ID:</label>
            <input type="text" name="params[erip_id]" value="<?=$params['erip_id'];?>"></p>
        <p><label>Логин:</label>
            <input type="text" name="params[login]" value="<?=$params['login'];?>"></p>
        <p><label>Пароль:</label>
            <input type="text" name="params[pass]" value="<?=$params['pass'];?>"></p>
        <div class="width-100"><label>Кнопка Alfaclick (для выставления счета в Alfaclick):</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[alfaclick]" type="radio" value="1" <?php if($params['alfaclick'] == 1) echo 'checked';?>><span>Вкл</span></label>
                <label class="custom-radio"><input name="params[alfaclick]" type="radio" value="0" <?php if($params['alfaclick'] == 0) echo 'checked';?>><span>Откл</span></label>
            </span></div>
        <div class="width-100"><label>Кнопка Webpay (для оплаты счета картой в Webpay):</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[webpay]" type="radio" value="1" <?php if($params['webpay'] == 1) echo 'checked';?>><span>Вкл</span></label>
                <label class="custom-radio"><input name="params[webpay]" type="radio" value="0" <?php if($params['webpay'] == 0) echo 'checked';?>><span>Откл</span></label>
            </span></div>
        <div class="width-100"><label>Sandbox (Режим *песочницы*):</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[sandbox]" type="radio" value="1" <?php if($params['sandbox'] == 1) echo 'checked';?>><span>Вкл</span></label>
                <label class="custom-radio"><input name="params[sandbox]" type="radio" value="0" <?php if($params['sandbox'] == 0) echo 'checked';?>><span>Откл</span></label>
            </span></div>
        <p><label>Срок действия счета (дней):</label>
            <input type="text" name="params[account_time]" value="<?=$params['account_time'];?>"></p>
        <div class="width-100"><label>Валюта заказа:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[currency]" type="radio" value="BYN" <?php if($params['currency'] == 'BYN') echo 'checked';?>><span>BYN</span></label>
               <label class="custom-radio"><input name="params[currency]" type="radio" value="USD" <?php if($params['currency'] == 'USD') echo 'checked';?>><span>USD</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="EUR" <?php if($params['currency'] == 'EUR') echo 'checked';?>><span>EUR</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="RUB" <?php if($params['currency'] == 'RUB') echo 'checked';?>><span>RUB</span></label>
            </span></div>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232759"><i class="icon-info"></i>Справка по расширению</a>
</div>