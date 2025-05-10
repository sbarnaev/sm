<?php defined('BILLINGMASTER') or die; ?>


<p><label>ID формы</label>
<select name="widget[params][form_id]">
    <?php $form_list = System::getFeedBackFormList();
    if($form_list):
    foreach($form_list as $form):?>
    <option value="<?php echo $form['form_id'];?>"><?php echo $form['name'];?></option>
    <?php endforeach;
    endif;?>
</select>
</p>