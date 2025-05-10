<?php defined('BILLINGMASTER') or die;
if(!empty($payment['params'])) $params = unserialize(base64_decode($payment['params']));
else $params = null;?>
<div class="row-line">
  <div class="col-1-2">
<h4 class="h4-border">Параметры</h4>
<div class="width-100"><label>Тип юрлица</label>
  <div class="select-wrap">
<select name="params[type_org]">
<option value="1"<?php if($params['type_org'] == 1) echo ' selected="selected"';?>><?php echo System::Lang('ORG_IP');?></option>
<option value="2"<?php if($params['type_org'] == 2) echo ' selected="selected"';?>><?php echo System::Lang('ORG_OOO');?></option>
</select>
</div>
</div>


<p><label>Наименование юрлица</label>
<input type="text" name="params[org_name]" value="<?php echo $params['org_name'];?>"></p>

<p><label>ИНН:</label>
<input type="text" name="params[inn]" value="<?php echo $params['inn'];?>"></p>

<p><label>КПП (для ООО):</label>
<input type="text" name="params[kpp]" value="<?php echo $params['kpp'];?>"></p>

<p><label>Полный юр.адрес (для ИП - адрес проживания) </label>
<textarea name="params[address]" cols="60" rows="2"><?php echo $params['address'];?></textarea></p>
  </div>

  <div class="col-1-2">
<h4>Банковские реквизиты</h4>
<p><label>Банк:</label>
<input type="text" name="params[bank_name]" value="<?php echo $params['bank_name'];?>"></p>

<p><label>БИК банка:</label>
<input type="text" name="params[bik]" value="<?php echo $params['bik'];?>"></p>

<p><label>Счёт банка:</label>
<input type="text" name="params[bank_schet]" value="<?php echo $params['bank_schet'];?>"></p>

<p><label>Ваш № расчётного счёта:</label>
<input type="text" name="params[your_rs]" value="<?php echo $params['your_rs'];?>"></p>

<p><label>НДС, %</label>
<input type="text" name="params[nds]" value="<?php echo $params['nds'];?>"></p>

<p><label>Срок оплаты счёта, дней</label>
<input type="text" name="params[pay_time]" value="<?php echo $params['pay_time'];?>"></p>

<h4>Подписи/печати</h4>
<p><label>Подпись руководителя:</label>
<input type="text" name="params[sign_boss]" style="width:80%" value="<?php echo $params['sign_boss'];?>">
<?php if(!empty($params['sign_boss'])):?><img style="width:17%" src="/images/<?php echo $params['sign_boss'];?>" alt=""><?php endif;?></p>

<p><label>Подпись бугалтера (для ООО):</label>
<input type="text" name="params[sign_buh]" style="width:80%" value="<?php echo $params['sign_buh'];?>">
<?php if(!empty($params['sign_buh'])):?><img style="width:17%" src="/images/<?php echo $params['sign_buh'];?>" alt=""><?php endif;?></p>

<p><label>Печать (для ИП не обязательно):</label>
<input type="text" name="params[print]" style="width:80%" value="<?php echo $params['print'];?>">
<?php if(!empty($params['print'])):?><img style="width:17%" src="/images/<?php echo $params['print'];?>" alt=""><?php endif;?></p>

<p><label>Координаты подписей:</label>
<textarea name="params[sign_css]" placeholder=".sign-1 {left:200px; top:24px} 
.sign-2 {left:200px; top:80px} 
.printing {left:271px; top: -15px}"></textarea>
</p>
  </div>
  <div class="col-1-1">
<h4>Дополнительно</h4>
<p><label>Инструкция</label>
<textarea class="editor" name="params[instruct]"><?php echo $params['instruct'];?></textarea></p>
  </div>
  <div class="col-1-1">
<p><label>Текст спасибо</label>
<textarea class="editor" name="params[thanks]"><?php echo $params['thanks'];?></textarea></p>
</div>
</div>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232781"><i class="icon-info"></i>Справка по расширению</a>
</div>