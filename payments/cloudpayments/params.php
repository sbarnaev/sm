<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>

<p><label>Public_ID</label><br />
<input type="text" name="params[public_id]" value="<?php echo $params['public_id'];?>"></p>

<p><label>Пароль для API:</label><br />
<input type="text" name="params[pass_api]" value="<?php echo $params['pass_api'];?>"></p>

<p><label>Валюта (RUB):</label><br />
<input type="text" name="params[currency]" value="<?php echo $params['currency'];?>"></p>

<p><label>Показывать чек бокс для автоплатежей:</label>
<select name="params[checkbox]">
<option value="1"<?php if($params['checkbox'] == 1) echo ' selected="selected"';?>>Показать</option>
<option value="0"<?php if($params['checkbox'] == 0) echo ' selected="selected"';?>>Не показывать</option>
</select></p>

<p>----------------</p>
<p><label>Онлайн касса:</label>
<select name="params[online_kassa]">
<option value="0"<?php if($params['online_kassa'] == 0) echo ' selected="selected"';?>>Отключена</option>
<option value="1"<?php if($params['online_kassa'] == 1) echo ' selected="selected"';?>>Подключена</option>
</select></p>

<p><label>Наименование организации (ФИО ИП):</label><br />
<input type="text" name="params[full_name]" value="<?php echo $params['full_name'];?>"></p>

<p><label>ИНН:</label><br />
<input type="text" name="params[inn]" value="<?php echo $params['inn'];?>"></p>

<p><label>Система налогообложения:</label>
<select name="params[taxationsystem]">
<option value="0"<?php if(isset($params['taxationsystem']) && $params['taxationsystem'] == 0) echo ' selected="selected"';?>>ОСН</option>
<option value="1"<?php if(isset($params['taxationsystem']) && $params['taxationsystem'] == 1) echo ' selected="selected"';?>>УСН (Доход)</option>
<option value="2"<?php if(isset($params['taxationsystem']) && $params['taxationsystem'] == 2) echo ' selected="selected"';?>>УСН (Доход - Расход)</option>
<option value="3"<?php if(isset($params['taxationsystem']) && $params['taxationsystem'] == 3) echo ' selected="selected"';?>>ЕНВД</option>
<option value="5"<?php if(isset($params['taxationsystem']) && $params['taxationsystem'] == 5) echo ' selected="selected"';?>>Патент</option>
</select></p>

<p><label>Предмет расчёта:</label>
<select name="params[object]">
<option value="1"<?php if(isset($params['object']) && $params['object'] == 1) echo ' selected="selected"';?>>Товар</option>
<option value="4"<?php if(isset($params['object']) && $params['object'] == 4) echo ' selected="selected"';?>>Услуга</option>
<option value="13"<?php if(isset($params['object']) && $params['object'] == 13) echo ' selected="selected"';?>>Иное</option>
</select></p>

<p><label>Ставка НДС:</label>
<select name="params[vat_code]">
<option value=""<?php if($params['vat_code'] === '') echo ' selected="selected"';?>>НДС не облагается</option>
<option value="0"<?php if($params['vat_code'] === 0) echo ' selected="selected"';?>>НДС по ставке 0%</option>
<option value="10"<?php if($params['vat_code'] == 10) echo ' selected="selected"';?>>НДС по ставке 10%</option>
<option value="20"<?php if($params['vat_code'] == 20) echo ' selected="selected"';?>>НДС чека по ставке 20%</option>
<option value="110"<?php if($params['vat_code'] == 110) echo ' selected="selected"';?>>НДС чека по расчетной ставке 10/110</option>
<option value="120"<?php if($params['vat_code'] == 120) echo ' selected="selected"';?>>НДС чека по расчетной ставке 20/120</option>
</select></p>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232534"><i class="icon-info"></i>Справка по расширению</a>
</div>