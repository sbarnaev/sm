<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>

<h4 class="h4-border">Параметры</h4>
<p><label>Список кошельков для приёма (через запятую)</label>
<textarea name="params[gateway]"><?php echo $params['gateway'];?></textarea></p>

<p><label>Инструкция</label>
<textarea class="editor" name="params[instruct]"><?php echo $params['instruct'];?></textarea></p>

<p><label>Текст спасибо</label>
<textarea class="editor" name="params[thanks]"><?php echo $params['thanks'];?></textarea></p>
<div class="reference-link">
    <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232780"><i class="icon-info"></i>Справка по расширению</a>
</div>