<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));
$pay_methods = array(
    'yoo_money' => 'Платеж из кошелька ЮMoney',
    'bank_card' => 'Платеж с произвольной банковской карты',
    'sberbank' => 'Платеж СбербанкОнлайн',
    'cash' => 'Платеж наличными',
    'mobile_balance' => 'Платеж с баланса мобильного телефона',
//    'apple_pay' => 'Платеж ApplePay',
//    'google_pay' => 'Платеж Google Pay',
    'qiwi' => 'Платеж из кошелька Qiwi',
    'webmoney' => 'Оплата на странице Webmoney',
    'alfabank' => 'Оплата на сайте Альфа-Клик',
    'b2b_sberbank' => 'Сбербанк Бизнес Онлайн',
    'tinkoff_bank' => 'Интернет-банк Тинькофф',
    'psb' => 'ПромсвязьБанк',
    'wechat' => 'Платеж через WeChat',
);
if (!isset($params['pay_methods'])) {
    $params['pay_methods'] = array();
}
if (!isset($params['sel_pay_mtds'])) {
    $params['sel_pay_mtds'] = 'defaut';
}?>

<div class="row-line">
  <div class="col-1-2">
    <h4 class="h4-border">Параметры</h4>
<p><label>Shop_ID</label>
<input type="text" name="params[ya_shop_id]" value="<?php echo $params['ya_shop_id'];?>"></p>

<p><label>Ключ API:</label>
<input type="text" name="params[api_key]" value="<?php echo $params['api_key'];?>"></p>

<p><label>Валюта (RUB):</label>
<input type="text" name="params[currency]" value="<?php echo $params['currency'];?>"></p>

<p><label>Онлайн касса:</label>
  <span class="custom-radio-wrap">
     <label class="custom-radio"><input name="params[online_kassa]" type="radio" value="1" <?php if($params['online_kassa'] == 1) echo 'checked';?>><span>Вкл</span></label>
     <label class="custom-radio"><input name="params[online_kassa]" type="radio" value="0" <?php if($params['online_kassa'] == 0) echo 'checked';?>><span>Откл</span></label>
  </span>
</p>

<p><label>Наименование организации (ФИО ИП):</label>
<input type="text" name="params[full_name]" value="<?php echo $params['full_name'];?>"></p>

<p><label>ИНН:</label>
<input type="text" name="params[inn]" value="<?php echo $params['inn'];?>"></p>

<div class="width-100"><label>Ставка НДС:</label>
  <div class="select-wrap">
     <select name="params[vat_code]">
        <option value="1"<?php if($params['vat_code'] == 1) echo ' selected="selected"';?>>Без НДС</option>
        <option value="2"<?php if($params['vat_code'] == 2) echo ' selected="selected"';?>>НДС по ставке 0%</option>
        <option value="3"<?php if($params['vat_code'] == 3) echo ' selected="selected"';?>>НДС по ставке 10%</option>
        <option value="4"<?php if($params['vat_code'] == 4) echo ' selected="selected"';?>>НДС чека по ставке 20%</option>
        <option value="5"<?php if($params['vat_code'] == 5) echo ' selected="selected"';?>>НДС чека по расчетной ставке 10/110</option>
        <option value="6"<?php if($params['vat_code'] == 6) echo ' selected="selected"';?>>НДС чека по расчетной ставке 20/120</option>
        </select>
     </div>
</div>

<div class="width-100 sel_pay_mtds"><label>Выбор способов оплаты:</label>
  <span class="custom-radio-wrap">
    <label class="custom-radio"><input name="params[sel_pay_mtds]" type="radio" value="defaut" <?php if($params['sel_pay_mtds'] == 'defaut') echo 'checked';?>><span>Выбор способа сайте Яндекс.Кассы</span></label>
    <label class="custom-radio"><input name="params[sel_pay_mtds]" type="radio" value="this_site" <?php if($params['sel_pay_mtds'] == 'this_site') echo 'checked';?>><span class="not-red">Выбор способа на этом сайте</span></label>
</span></div>

<p style="display:none;" class="pay-methods-box"><label>Способы оплаты:</label>
<select class="multiple-select" name="params[pay_methods][]" multiple="multiple">
  <?php foreach ($pay_methods as $key => $name):?>
  <option value="<?php echo $key;?>"<?php if(!empty($params['pay_methods']) && array_search($key, $params['pay_methods']) !== false) echo ' selected="selected"';?>>
      <?php echo $name;?>
  </option>
  <?php endforeach;?>
</select></p>
  </div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232148"><i class="icon-info"></i>Справка по расширению</a>
</div>

<script>
  $(function() {
    $('.sel_pay_mtds').find('input[name="params[sel_pay_mtds]"]').each(function() {
      if ($(this).val() == 'this_site' && $(this).is(':checked')) {
        $('.pay-methods-box').show();
      }
    });
    $('.sel_pay_mtds').find('input[name="params[sel_pay_mtds]"]').click(function() {
      if ($(this).val() == 'this_site' && $(this).is(':checked')) {
        $('.pay-methods-box').show(100);
      } else {
        $('.pay-methods-box').hide(100);
      }
    });
  });
</script>