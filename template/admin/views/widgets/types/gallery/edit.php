<?php defined('BILLINGMASTER') or die; ?>

<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Содержимое</h4>
    </div>

    <div class="col-1-2">
        <p><label>Выбор категории:</label>
            <select name="widget[params][category]">
                <?php $cat_list = Gallery::getCatList();
                if($cat_list):
                    foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id']?>"<?php if($params['params']['category'] == $cat['cat_id']) echo ' selected="selected"';?>><?php echo $cat['cat_name']?></option>
                    <?php endforeach;
                endif;?>
            </select>
        </p>

        <p><label>Выбор макета виджета:</label>
        <select name="widget[params][style]">
            <option value="columns"<?php if($params['params']['style'] == 'columns') echo ' selected="selected"';?>>Колонки</option>
            <option value="justified"<?php if($params['params']['style'] == 'justified') echo ' selected="selected"';?>>Ряды</option>
            <option value="grid"<?php if($params['params']['style'] == 'grid') echo ' selected="selected"';?>>Плитка</option>
            <option value="carousel"<?php if($params['params']['style'] == 'carousel') echo ' selected="selected"';?>>Карусель</option>
            <option value="slider"<?php if($params['params']['style'] == 'slider') echo ' selected="selected"';?>>Слайдер</option>
        </select></p>

        <p><label>Выборка фото:</label>
            <select name="widget[params][source]">
                <option value="category"<?php if($params['params']['source'] == 'category') echo ' selected="selected"';?>>из категории</option>
                <option value="folder"<?php if($params['params']['source'] == 'folder') echo ' selected="selected"';?>>из папки</option>
            </select>
        </p>

        <p><label>Папка с изображениями:</label>
            <input type="text" value="<?php echo $params['params']['folder'];?>" name="widget[params][folder]">
        </p>

        <p><br /></p>
    </div>
</div>