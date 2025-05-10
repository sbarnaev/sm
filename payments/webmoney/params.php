<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>Номер кошелька:</label>
            <input type="text" name="params[purse_number]" value="<?=$params['purse_number'];?>"></p>
        <p><label>Секретный код</label>
            <input type="text" name="params[secret_key]" value="<?=$params['secret_key'];?>"></p>
        <div class="width-100"><label>Тестовый режим:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[test_mode]" type="radio" value="0" <?php if(!$params['test_mode']) echo 'checked';?>><span>Выключен</span></label>
                <label class="custom-radio"><input name="params[test_mode]" type="radio" value="1" <?php if($params['test_mode']) echo 'checked';?>><span>Включен</span></label>
            </span></div>
        <div class="width-100"><label>Режим тестирования:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="params[sim_mode]" type="radio" value="0" <?php if(!$params['sim_mode']) echo 'checked';?>><span>Для всех тестовых платежей сервис будет имитировать успешное выполнение</span></label>
                <label class="custom-radio"><input name="params[sim_mode]" type="radio" value="1" <?php if($params['sim_mode'] == 1) echo 'checked';?>><span>Для всех тестовых платежей сервис будет имитировать выполнение с ошибкой (платеж не выполнен)</span></label>
                <label class="custom-radio"><input name="params[sim_mode]" type="radio" value="2" <?php if($params['sim_mode'] == 2) echo 'checked';?>><span>Около 80% запросов на платеж будут выполнены успешно, а 20% - не выполнены</span></label>
            </span></div>
    </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232777"><i class="icon-info"></i>Справка по расширению</a>
</div>