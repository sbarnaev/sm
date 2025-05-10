<?php defined('BILLINGMASTER') or die; ?>
<div class="width-100"><label>Ссылка на мои заказы:</label>

        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="widget[params][myorders]" type="radio" value="1" <?php if($params['params']['myorders'] == 1) echo 'checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input name="widget[params][myorders]" type="radio" value="0" <?php if($params['params']['myorders'] == 0) echo 'checked';?>><span>Нет</span></label>
                    </span>

</div>

<div class="width-100"><label>Ориентация модуля:</label>
    <div class="select-wrap">
<select name="widget[params][orient]">
    <option value="gorizontal"<?php if($params['params']['orient'] == 'gorizontal') echo ' selected="selected"';?>>Горизонтально</option>
    <option value="vertical"<?php if($params['params']['orient'] == 'vertical') echo ' selected="selected"';?>>Вертикально</option>
</select>
</div>
</div>