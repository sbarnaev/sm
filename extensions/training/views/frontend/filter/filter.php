<?php  defined('BILLINGMASTER') or die;
$acc_filter = isset($_GET['acc']) && $_GET['acc'] != 'all' ? $_GET['acc'] : null;
$cat_filter = isset($_GET['cat']) && is_array($_GET['cat']) && array_filter($_GET['cat'], 'strlen') ? $_GET['cat'] : [];
$aut_filter = isset($_GET['aut']) && is_array($_GET['aut']) && array_filter($_GET['aut'], 'strlen')  ? $_GET['aut'] : [];

if(isset($this->tr_settings['filter'])):
    $filter_settings = $this->tr_settings['filter'];
elseif(isset($widget_params['params']['filter'])):
    $filter_settings = $widget_params['params']['filter'];
endif;?>

<div class="course_filter">
    <?php if (count($filter_settings) == 1 && in_array('access', $filter_settings)):?>
        <ul class="access_filter">
            <li class="access_filter_item<?=!$acc_filter ? ' active' : '';?>" data-access="all"><a href="?acс=all"><?=System::Lang('ALL_COURSES');?></a></li>
            <li class="access_filter_item<?=$acc_filter == 'paid' ? ' active' : '';?>" data-access="paid"><a href="?acс=paid"><?=System::Lang('PAID_COURSES');?></a></li>
            <li class="access_filter_item<?=$acc_filter == 'free' ? ' active' : '';?>" data-access="free"><a href="?acс=free"><?=System::Lang('FREE_COURSES');?></a></li>
        </ul>
    <?php else:?>
        <div class="filter-select">
            <div class="filter-select-row">
                <?php foreach ($filter_settings as $filter):
                    switch($filter):
                        case 'access':?>
                            <div class="filter-select-col">
                                <div>
                                    <select class="training_filter select2" name="access_filter">
                                        <option value="all" data-filter="acc"<?=!$acc_filter ? ' selected="selected"' : '';?>><?=System::Lang('ALL_COURSES');?></option>
                                        <option value="paid" data-filter="acc"<?=$acc_filter == 'paid' ? ' selected="selected"' : '';?>><?=System::Lang('PAID_COURSES');?></option>
                                        <option value="free" data-filter="acc"<?=$acc_filter == 'free' ? ' selected="selected"' : '';?>><?=System::Lang('FREE_COURSES');?></option>
                                    </select>
                                </div>
                            </div>
                            <?php break;
                        case 'category':
                            $categories = TrainingCategory::getCatList();
                            if ($categories):?>
                                <div class="filter-select-col">
                                    <div class="multiple">
                                        <select class="training_filter select2" name="category_filter[]" multiple="multiple">
                                            <option value="" data-filter="cat[]"<?=empty($cat_filter) ? ' selected="selected"' : '';?>><?=System::Lang('ALL_CATEGORIES');?></option>
                                            <?php foreach($categories as $category):?>
                                                <option value="<?=$category['cat_id'];?>" data-filter="cat[]"<?=in_array($category['cat_id'], $cat_filter) ? ' selected="selected"' : '';?>><?=$category['name'];?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif;
                            break;
                        case 'author':
                            $authors = User::getAuthors();
                            if ($authors):?>
                                <div class="filter-select-col">
                                    <div class="multiple">
                                        <select class="training_filter select2" name="author_filter[]" multiple="multiple">
                                            <option value="" data-filter="aut[]"<?=empty($aut_filter) ? ' selected="selected"' : '';?>><?=System::Lang('ALL_AUTHORS');?></option>
                                            <?php foreach($authors as $author):?>
                                                <option value="<?=$author['user_id'];?>" data-filter="aut[]"<?=in_array($author['user_id'], $aut_filter) ? ' selected="selected"' : '';?>><?=$author['user_name'];?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif;
                            break;
                    endswitch;
                endforeach;?>
            </div>
        </div>
    <?php endif;?>
</div>