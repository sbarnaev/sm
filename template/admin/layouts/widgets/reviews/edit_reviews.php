<?php defined('BILLINGMASTER') or die; ?>
<p><label>Выводить отзывы из категории:</label>
<select name="widget[params][category]">
    <option value="0">Не выводить из категории</option>
    <?php $cat_list = Product::getReviewsCats();
    if($cat_list):
    foreach($cat_list as $cat):?>
    <option value="<?php echo $cat['cat_id'];?>"<?php if($params['params']['category'] == $cat['cat_id']) echo ' selected="selected"';?>><?php echo $cat['cat_name'];?></option>
    <?php endforeach;
    endif;?>
</select>
</p>


<p><label>Выводить отзывы по метке:</label>
<select name="widget[params][label]">
    <option value="0">Не выводить по метке</option>
    <?php $label_list = Product::getReviewsLabels();
    if($label_list):
    foreach($label_list as $label):?>
    <option value="<?php echo $label['label_id'];?>"<?php if($params['params']['label'] == $label['label_id']) echo ' selected="selected"';?>><?php echo $label['label_name'];?></option>
    <?php endforeach;
    endif;?>
</select>
</p>

<p><label>Кол-во отзывов:</label><input type="text" name="widget[params][countreviews]" value="<?php echo $params['params']['countreviews'];?>"></p>
<p><label>Ориентация виджета:</label>
<select name="widget[params][orient]">
    <option value="gorizontal"<?php if($params['params']['orient'] == 'gorizontal') echo ' selected="selected"';?>>Горизонтальная</option>
    <option value="vertical"<?php if($params['params']['orient'] == 'vertical') echo ' selected="selected"';?>>Вертикальная</option>
</select>
</p>