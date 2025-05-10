<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));
?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>Идентификатор магазина:</label>
            <input type="text" name="params[merchant_id]" value="<?=$params['merchant_id'];?>"></p>
        <p><label>Секретный ключ магазина:</label>
            <input type="text" name="params[secret_key]" value="<?=$params['secret_key'];?>"></p>
        <div class="width-100"><label>Валюта заказа:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[currency]" type="radio" value="RUB" <?php if($params['currency'] == 'RUB') echo 'checked';?>><span>RUB</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="USD" <?php if($params['currency'] == 'USD') echo 'checked';?>><span>USD</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="EUR" <?php if($params['currency'] == 'EUR') echo 'checked';?>><span>EUR</span></label>
                <label class="custom-radio"><input name="params[currency]" type="radio" value="CNY" <?php if($params['currency'] == 'CNY') echo 'checked';?>><span>CNY</span></label>
            </span></div>
        <div class="width-100"><label>Схема проведения платежа:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[payment_mode]" type="radio" value="default" <?php if($params['payment_mode'] == 'default') echo 'checked';?>><span>Одностадийная оплата</span></label>
                <label class="custom-radio"><input name="params[payment_mode]" type="radio" value="hold" <?php if($params['payment_mode'] == 'hold') echo 'checked';?>><span>Двухстадийная оплата</span></label>
            </span></div>
        <p><label>Система налогообложения:</label>
            <select name="params[SNO]">
                <option value=""<?php if(!$params['SNO']) echo ' selected="selected"';?>></option>
                <option value="osn"<?php if($params['SNO'] == 'osn') echo ' selected="selected"';?>>Общая</option>
                <option value="usn_income"<?php if($params['SNO'] == 'usn_income') echo ' selected="selected"';?>>Упрощенная СН (доходы)</option>
                <option value="usn_income_outcome"<?php if($params['SNO'] == 'usn_income_outcome') echo ' selected="selected"';?>>Упрощенная СН (доходы минус расходы)</option>
                <option value="envd"<?php if($params['SNO'] == 'envd') echo ' selected="selected"';?>>Единый налог на вмененный доход</option>
                <option value="esn"<?php if($params['SNO'] == 'esn') echo ' selected="selected"';?>>Единый сельскохозяйственный налог</option>
                <option value="patent"<?php if($params['SNO'] == 'patent') echo ' selected="selected"';?>>Патентная СН</option>
            </select></p>
        <p><label>Ставка НДС:</label>
            <select name="params[tax_type]">
                <option value="none"<?php if($params['tax_type'] == 'none') echo ' selected="selected"';?>>без НДС</option>
                <option value="vat0"<?php if($params['tax_type'] == 'vat0') echo ' selected="selected"';?>>НДС по ставке 0%</option>
                <option value="vat10"<?php if($params['tax_type'] == 'vat10') echo ' selected="selected"';?>>НДС по ставке 10%</option>
                <option value="vat18"<?php if($params['tax_type'] == 'vat18') echo ' selected="selected"';?>>НДС по ставке 18%</option>
                <option value="vat20"<?php if($params['tax_type'] == 'vat20') echo ' selected="selected"';?>>НДС по ставке 20%</option>
                <option value="vat110"<?php if($params['tax_type'] == 'vat110') echo ' selected="selected"';?>>НДС по расчетной ставке 10/118</option>
                <option value="vat118"<?php if($params['tax_type'] == 'vat118') echo ' selected="selected"';?>>НДС по расчетной ставке 18/118</option>
            </select></p>
        <p><label>Предмет расчета:</label>
            <select name="params[payment_object]">
                <option value=""<?php if(!$params['payment_object']) echo ' selected="selected"';?>></option>
                <option value="commodity"<?php if($params['payment_object'] == 'commodity') echo ' selected="selected"';?>>Товар</option>
                <option value="excise"<?php if($params['payment_object'] == 'excise') echo ' selected="selected"';?>>Подакцизный товар</option>
                <option value="job"<?php if($params['payment_object'] == 'job') echo ' selected="selected"';?>>Работа</option>
                <option value="service"<?php if($params['payment_object'] == 'service') echo ' selected="selected"';?>>Услуга</option>
                <option value="gambling_bet"<?php if($params['payment_object'] == 'gambling_bet') echo ' selected="selected"';?>>Ставка азартной игры</option>
                <option value="gambling_prize"<?php if($params['payment_object'] == 'gambling_prize') echo ' selected="selected"';?>>Выигрыш азартной игры</option>
                <option value="lottery"<?php if($params['payment_object'] == 'lottery') echo ' selected="selected"';?>>Лотерейный билет</option>
                <option value="lottery_prize"<?php if($params['payment_object'] == 'lottery_prize') echo ' selected="selected"';?>>Выигрыш лотереи</option>
                <option value="intellectual_activity"<?php if($params['payment_object'] == 'intellectual_activity') echo ' selected="selected"';?>>Предоставление результатов интеллектуальной деятельности</option>
                <option value="payment"<?php if($params['payment_object'] == 'payment') echo ' selected="selected"';?>>Платеж</option>
                <option value="agent_commission"<?php if($params['payment_object'] == 'agent_commission') echo ' selected="selected"';?>>Агентское вознаграждение</option>
                <option value="composite"<?php if($params['payment_object'] == 'composite') echo ' selected="selected"';?>>Составной предмет расчета</option>
                <option value="another"<?php if($params['payment_object'] == 'another') echo ' selected="selected"';?>>Другое</option>
            </select></p>
        <div class="width-100"><label>Тестовый режим:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[test_mode]" type="radio" value="1" <?php if($params['test_mode']) echo 'checked';?>><span>Включен</span></label>
                <label class="custom-radio"><input name="params[test_mode]" type="radio" value="" <?php if(!$params['test_mode']) echo 'checked';?>><span>Выключен</span></label>
            </span></div>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232779"><i class="icon-info"></i>Справка по расширению</a>
</div>