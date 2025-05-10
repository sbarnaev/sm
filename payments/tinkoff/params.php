<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>Terminal_Key:</label>
            <input type="text" name="params[terminal_key]" value="<?=$params['terminal_key'];?>">
        </p>

        <p><label>Secret_Key</label>
            <input type="text" name="params[secret_key]" value="<?=$params['secret_key'];?>">
        </p>

        <p><label>Ставка НДС:</label>
            <select name="params[tax_type]">
                <option value="none"<?php if(!isset($params['tax_type']) || $params['tax_type'] == 'none') echo ' selected="selected"';?>>Без НДС</option>
                <option value="vat0"<?php if(isset($params['tax_type']) && $params['tax_type'] == 'vat0') echo ' selected="selected"';?>>НДС по ставке 0%</option>
                <option value="vat10"<?php if(isset($params['tax_type']) && $params['tax_type'] == 'vat10') echo ' selected="selected"';?>>НДС по ставке 10%</option>
                <option value="vat20"<?php if(isset($params['tax_type']) && $params['tax_type'] == 'vat20') echo ' selected="selected"';?>>НДС по ставке 20%</option>
            </select>
        </p>

        <p><label>Система налогообложения:</label>
            <select name="params[SNO]">
                <option value="osn"<?php if(!isset($params['SNO']) || $params['SNO'] == 'osn') echo ' selected="selected"';?>>Общая СН</option>
                <option value="usn_income"<?php if(isset($params['SNO']) && $params['SNO'] == 'usn_income') echo ' selected="selected"';?>>Упрощенная СН (доходы)</option>
                <option value="usn_income_outcome"<?php if(isset($params['SNO']) && $params['SNO'] == 'usn_income_outcome') echo ' selected="selected"';?>>Упрощенная СН (доходы минус расходы)</option>
                <option value="envd"<?php if(isset($params['SNO']) && $params['SNO'] == 'envd') echo ' selected="selected"';?>>Единый налог на вмененный доход</option>
                <option value="esn"<?php if(isset($params['SNO']) && $params['SNO'] == 'esn') echo ' selected="selected"';?>>Единый сельскохозяйственный налог</option>
                <option value="patent"<?php if(isset($params['SNO']) && $params['SNO'] == 'patent') echo ' selected="selected"';?>>Патентная СН</option>
            </select>
        </p>
        
        <p><label>Тип рассчёта:</label>
            <select name="params[payment_method]">
                <option value="full_payment"<?php if(isset($params['payment_method']) && $params['payment_method'] == 'full_payment') echo ' selected="selected"';?>>Полный расчёт</option>
                <option value="full_prepayment"<?php if(!isset($params['payment_method']) || $params['payment_method'] == 'full_prepayment') echo ' selected="selected"';?>>Предоплата 100%</option>
                <option value="prepayment"<?php if(isset($params['payment_method']) && $params['payment_method'] == 'prepayment') echo ' selected="selected"';?>>Предоплата</option>
                <option value="credit"<?php if(isset($params['payment_method']) && $params['payment_method'] == 'credit') echo ' selected="selected"';?>>Кредит</option>
            </select>
        </p>
        
        <p><label>Тип товара:</label>
            <select name="params[payment_object]">
                <option value="commodity"<?php if(isset($params['payment_object']) && $params['payment_object'] == 'commodity') echo ' selected="selected"';?>>Товар</option>
                <option value="service"<?php if(!isset($params['payment_object']) || $params['payment_object'] == 'service') echo ' selected="selected"';?>>Услуга</option>
                <option value="payment"<?php if(isset($params['payment_object']) && $params['payment_object'] == 'payment') echo ' selected="selected"';?>>Платёж</option>
                <option value="another"<?php if(isset($params['payment_object']) && $params['payment_object'] == 'another') echo ' selected="selected"';?>>Другое</option>
            </select>
        </p>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232535"><i class="icon-info"></i>Справка по расширению</a>
</div>