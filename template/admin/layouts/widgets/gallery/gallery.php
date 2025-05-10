<?php defined('BILLINGMASTER') or die; ?>

<p><label>Выбор категории:</label>
<select name="widget[params][category]">
    <?php $cat_list = Gallery::getCatList();
    if($cat_list):
    foreach($cat_list as $cat):?>
    <option value="<?php echo $cat['cat_id']?>"><?php echo $cat['cat_name']?></option>
    
    <?php endforeach;
    endif;?>
</select></p>

<p><label>Выбор макета виджета:</label>
<select name="widget[params][style]">
    <option value="columns">Колонки</option>
    <option value="justified">Ряды</option>
    <option value="grid">Плитка</option>
    <option value="slider">Слайдер</option>
    <option value="carousel">Карусель</option>
</select></p>


<p><label>Выборка фото:</label>
<select name="widget[params][source]">
    <option value="category">из категории</option>
    <option value="folder">из папки</option>
</select></p>

<p><label>Папка с изображениями:</label><input type="text" value="images" name="widget[params][folder]"></p>

<p><br /></p>