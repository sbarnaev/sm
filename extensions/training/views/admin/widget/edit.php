<?php defined('BILLINGMASTER') or die;?>

<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Содержимое</h4>
    </div>

    <div class="col-1-2">
        <div class="width-100"><label>Что выводить:</label>
            <div class="select-wrap">
                <select name="widget[params][show_list]">
                    <option value="all"<?php if(isset($params['params']['show_list']) && $params['params']['show_list'] == 'all') echo ' selected="selected"';?>>Все с категориями</option>
                    <option value="without_categories"<?php if(isset($params['params']['show_list']) && $params['params']['show_list'] == 'without_categories') echo ' selected="selected"';?>>Все без разбивки на категории</option>
                    <option value="content_separate_category" data-show_on="categories_to_content" <?php if(isset($params['params']['show_list']) && $params['params']['show_list'] == 'content_separate_category') echo ' selected="selected"';?>>Содержимое отдельной категории</option>
                </select>
            </div>
        </div>

        <div class="width-100 hidden" id="categories_to_content">
            <label>Выбрать категории</label>
            <select size="7" class="multiple-select" multiple="multiple" name="widget[params][categories_to_content][]">
                <?php $categories = TrainingCategory::getCatList();
                if($categories):
                    foreach($categories as $category):?>
                        <option value="<?=$category['cat_id'];?>"<?php if(isset($params['params']['categories_to_content']) && in_array($category['cat_id'], $params['params']['categories_to_content'])) echo ' selected="selected"';?>><?=$category['name'];?></option>
                    <?php endforeach;
                endif;?>
            </select>
        </div>

        <div class="width-100">
            <label>Шаблон страницы</label>
            <select name="widget[params][template]">
                <option value="2columns"<?php if(isset($params['params']['template']) && $params['params']['template'] == '2columns') echo ' selected="selected"';?>>В 2 колонки</option>
                <option value="3columns"<?php if(isset($params['params']['template']) && $params['params']['template'] == '3columns') echo ' selected="selected"';?>>В 3 колонки</option>
            </select>
        </div>
    </div>

    <div class="col-1-2">
        <div class="width-100"><label>Фильтр</label>
            <select class="multiple-select" name="widget[params][filter][]" multiple="multiple" size="4">
                <option value="access"<?php if(isset($params['params']['filter']) && in_array('access', $params['params']['filter'])) echo ' selected="selected"';?>>По доступу</option>
                <option value="category"<?php if(isset($params['params']['filter']) && in_array('category', $params['params']['filter'])) echo ' selected="selected"';?>>По категории</option>
                <option value="author"<?php if(isset($params['params']['filter']) && in_array('author', $params['params']['filter'])) echo ' selected="selected"';?>>По автору</option>
            </select>
        </div>
    </div>
</div>