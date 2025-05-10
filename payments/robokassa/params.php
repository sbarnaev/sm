<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));
$payment_method = isset($params['payment_method']) ? $params['payment_method'] :'full_prepayment';
$payment_object = isset($params['payment_object']) ? $params['payment_object'] : 'commodity';
$tax = isset($params['tax']) ? $params['tax'] : 'none';
$sno = isset($params['sno']) ? $params['sno'] : 'osn';
$pay_object_delivery = isset($params['pay_object_delivery']) ? $params['pay_object_delivery'] : 'commodity';
?>
<div class="row-line">
  <div class="col-1-2">
    <h4 class="h4-border">Параметры</h4>
    <p><label>Логин мерчанта</label>
      <input type="text" name="params[login]" value="<?php echo $params['login'];?>"></p>

    <p><label>Пароль 1:</label>
      <input type="text" name="params[pass1]" value="<?php echo $params['pass1'];?>"></p>

    <p><label>Пароль 2:</label>
    <input type="text" name="params[pass2]" value="<?php echo $params['pass2'];?>"></p>
    <p><label>способа расчёта:</label>
      <select name="params[payment_method]">
        <option value="full_prepayment"<?php if($payment_method == 'full_prepayment') echo ' selected="selected"';?>>Предоплата 100%</option>
        <option value="prepayment"<?php if($payment_method == 'prepayment') echo ' selected="selected"';?>>Частичная предварительная оплата</option>
        <option value="advance"<?php if($payment_method == 'advance') echo ' selected="selected"';?>>Аванс</option>
        <option value="full_payment"<?php if($payment_method == 'full_payment') echo ' selected="selected"';?>>Полный расчёт (полная оплата, в том числе с учетом аванса)</option>
        <option value="partial_payment"<?php if($payment_method == 'partial_payment') echo ' selected="selected"';?>>Частичный расчёт и кредит</option>
        <option value="credit"<?php if($payment_method == 'credit') echo ' selected="selected"';?>>Передача в кредит.</option>
        <option value="credit_payment"<?php if($payment_method == 'credit_payment') echo ' selected="selected"';?>>Оплата кредита</option>
      </select></p>

    <p><label>Предмета расчёта:</label>
      <select name="params[payment_object]">
        <option value="commodity"<?php if($payment_object == 'commodity') echo ' selected="selected"';?>>Товар</option>
        <option value="excise"<?php if($payment_object == 'excise') echo ' selected="selected"';?>>Подакцизный товар</option>
        <option value="job"<?php if($payment_object == 'job') echo ' selected="selected"';?>>Работа</option>
        <option value="service"<?php if($payment_object == 'service') echo ' selected="selected"';?>>Услуга</option>
        <option value="gambling_bet"<?php if($payment_object == 'gambling_bet') echo ' selected="selected"';?>>Ставка азартной игры</option>
        <option value="gambling_prize"<?php if($payment_object == 'gambling_prize') echo ' selected="selected"';?>>Выигрыш азартной игры</option>
        <option value="lottery"<?php if($payment_object == 'lottery') echo ' selected="selected"';?>>Лотерейный билет</option>
        <option value="lottery_prize"<?php if($payment_object == 'lottery_prize') echo ' selected="selected"';?>>Выигрыш лотереи</option>
        <option value="intellectual_activity"<?php if($payment_object == 'intellectual_activity') echo ' selected="selected"';?>>Предоставление результатов интеллектуальной деятельности</option>
        <option value="payment"<?php if($payment_object == 'payment') echo ' selected="selected"';?>>Платеж</option>
        <option value="agent_commission"<?php if($payment_object == 'agent_commission') echo ' selected="selected"';?>>Агентское вознаграждение</option>
        <option value="composite"<?php if($payment_object == 'composite') echo ' selected="selected"';?>>Составной предмет расчета</option>
        <option value="another"<?php if($payment_object == 'another') echo ' selected="selected"';?>>Иной предмет расчета</option>
        <option value="property_right"<?php if($payment_object == 'property_right') echo ' selected="selected"';?>>Имущественное право</option>
        <option value="non-operating_gain"<?php if($payment_object == 'non-operating_gain') echo ' selected="selected"';?>>Внереализационный доход</option>
        <option value="insurance_premium"<?php if($payment_object == 'insurance_premium') echo ' selected="selected"';?>>Страховые взносыо</option>
        <option value="sales_tax"<?php if($payment_object == 'sales_tax') echo ' selected="selected"';?>>Торговый сбор</option>
        <option value="resort_fee"<?php if($payment_object == 'resort_fee') echo ' selected="selected"';?>>Курортный сбор</option>
      </select></p>

    <p><label>Предмет расчёта для доставки:</label>
      <select name="params[pay_object_delivery]">
        <option value="commodity"<?php if($pay_object_delivery == 'commodity') echo ' selected="selected"';?>>Товар</option>
        <option value="excise"<?php if($pay_object_delivery == 'excise') echo ' selected="selected"';?>>Подакцизный товар</option>
        <option value="job"<?php if($pay_object_delivery == 'job') echo ' selected="selected"';?>>Работа</option>
        <option value="service"<?php if($pay_object_delivery == 'service') echo ' selected="selected"';?>>Услуга</option>
        <option value="gambling_bet"<?php if($pay_object_delivery == 'gambling_bet') echo ' selected="selected"';?>>Ставка азартной игры</option>
        <option value="gambling_prize"<?php if($pay_object_delivery == 'gambling_prize') echo ' selected="selected"';?>>Выигрыш азартной игры</option>
        <option value="lottery"<?php if($pay_object_delivery == 'lottery') echo ' selected="selected"';?>>Лотерейный билет</option>
        <option value="lottery_prize"<?php if($pay_object_delivery == 'lottery_prize') echo ' selected="selected"';?>>Выигрыш лотереи</option>
        <option value="intellectual_activity"<?php if($pay_object_delivery == 'intellectual_activity') echo ' selected="selected"';?>>Предоставление результатов интеллектуальной деятельности</option>
        <option value="payment"<?php if($pay_object_delivery == 'payment') echo ' selected="selected"';?>>Платеж</option>
        <option value="agent_commission"<?php if($pay_object_delivery == 'agent_commission') echo ' selected="selected"';?>>Агентское вознаграждение</option>
        <option value="composite"<?php if($pay_object_delivery == 'composite') echo ' selected="selected"';?>>Составной предмет расчета</option>
        <option value="another"<?php if($pay_object_delivery == 'another') echo ' selected="selected"';?>>Иной предмет расчета</option>
        <option value="property_right"<?php if($pay_object_delivery == 'property_right') echo ' selected="selected"';?>>Имущественное право</option>
        <option value="non-operating_gain"<?php if($pay_object_delivery == 'non-operating_gain') echo ' selected="selected"';?>>Внереализационный доход</option>
        <option value="insurance_premium"<?php if($pay_object_delivery == 'insurance_premium') echo ' selected="selected"';?>>Страховые взносыо</option>
        <option value="sales_tax"<?php if($pay_object_delivery == 'sales_tax') echo ' selected="selected"';?>>Торговый сбор</option>
        <option value="resort_fee"<?php if($pay_object_delivery == 'resort_fee') echo ' selected="selected"';?>>Курортный сбор</option>
      </select></p>

    <p><label>Система налогообложения:</label>
      <select name="params[sno]">
        <option value="osn"<?php if($sno == 'osn') echo ' selected="selected"';?>>общая СН</option>
        <option value="usn_income"<?php if($sno == 'usn_income') echo ' selected="selected"';?>>упрощенная СН (доходы)</option>
        <option value="usn_income_outcome"<?php if($sno == 'usn_income_outcome') echo ' selected="selected"';?>>упрощенная СН (доходы минус расходы)</option>
        <option value="envd"<?php if($sno == 'envd') echo ' selected="selected"';?>>единый налог на вмененный доход</option>
        <option value="esn"<?php if($sno == 'esn') echo ' selected="selected"';?>>единый сельскохозяйственный налог</option>
        <option value="patent"<?php if($sno == 'patent') echo ' selected="selected"';?>>патентная СН</option>
      </select></p>

    <p><label>Ставка НДС:</label>
      <select name="params[tax]">
        <option value="none"<?php if($tax == 'none') echo ' selected="selected"';?>>без НДС</option>
        <option value="vat0"<?php if($tax == 'vat0') echo ' selected="selected"';?>>НДС по ставке 0%</option>
        <option value="vat10"<?php if($tax == 'vat10') echo ' selected="selected"';?>>НДС по ставке 10%</option>
        <option value="vat20"<?php if($tax == 'vat20') echo ' selected="selected"';?>>НДС по ставке 20%</option>
        <option value="vat110"<?php if($tax == 'vat110') echo ' selected="selected"';?>>НДС по расчетной ставке 10/110</option>
        <option value="vat120"<?php if($tax == 'vat118') echo ' selected="selected"';?>>НДС по расчетной ставке 20/120</option>
      </select></p>
  </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232552"><i class="icon-info"></i>Справка по расширению</a>
</div>