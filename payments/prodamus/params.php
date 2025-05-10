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

    <p><label>Адрес платежной страницы</label>
      <input type="text" name="params[prodamus_site_name]" value="<?php echo $params['prodamus_site_name'];?>"></p>
      
    <p><label>Секретный ключ</label>
      <input type="text" name="params[prodamus_secret_key]" value="<?php echo $params['prodamus_secret_key'];?>"></p>

  
  </div>
</div>
<h4 class="h4-border">Методы оплат</h4>
      <div class="col-1-1">
        Вводить через зяпятую, если оставить пустым, будут доступны методы которые настроены вами в продамусе
          <br>&nbsp;<br>
          AC - банковская карта
          <br>
          PC - Яндекс.Деньги
          <br>
          QW - Qiwi Wallet
          <br>
          WM - Webmoney
          <br>
          GP - платежный терминал
          <br>
          sbol - Сбербанк онлайн
          <br>
          invoice - Оплата по счету
          <br>
          installment_0_0_3 - Рассрочка от Тинькофф на 3 месяца
          <br>
          installment_0_0_6 - Рассрочка от Тинькофф на 6 месяцев
          <br>
          installment_0_0_10 - Рассрочка от Тинькофф на 10 месяцев
          <br>
          installment_0_0_12 - Рассрочка от Тинькофф на 12 месяцев
          <br>
          installment_0_0_24 - Рассрочка от Тинькофф на 24 месяца
          <br>
          installment_0_0_36 - Рассрочка от Тинькофф на 36 месяцев
      </div>
     <p><input type="text" name="params[available_payment_methods]" placeholder='для примера AC,PC,QW ' value="<?php if(isset($params['available_payment_methods'])) echo $params['available_payment_methods'];?>"></p>
      
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232107"><i class="icon-info"></i>Справка по расширению</a>
</div>