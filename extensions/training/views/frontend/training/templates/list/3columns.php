<?php defined('BILLINGMASTER') or die;

if ((isset($this->tr_settings['filter']) || isset($widget_params['params']['filter'])) && $is_page != 'my_trainings') {
    require_once(__DIR__ . '/../../../filter/filter.php');
    $training_filter_enabled = true;
}?>

<div class="row training_list" data-uk-grid-match=" { target:'.course_cover' } ">
    <?php if($training_list):
        $setting = isset($this->setting) ? $this->setting : System::getSetting();
        $tr_index = 0;
        foreach($training_list as $key => $training):
            if ((!$training['show_in_main']) && ($current_url !== "lk/mytrainings")) {
                continue;
            };

            $access = Training::getAccessData($user_groups, $user_planes, $training);
            $buttons = Training::renderByButtons($access, $training);?>

            <div class="col-1-3 course_item col-1-3__training-2">
                <div class="course_item__top <?php if(empty($training['cover'])):?>course_item__top--mt-25<?php endif;?>">
                    <h2 class="course_item__title"><?=$training['name'];?></h2>
                    <?php if($training['show_price']):?>
                        <span class="course_item__price"><?=!empty($training['price']) ? "{$training['price']}" : System::Lang('FREE');?></span>
                    <?php endif;

                    if(!empty($training['authors'])):?>
                        <p class="course_author"><?=System::Lang('AUTHOR');?>
                            <?php foreach(explode(',', $training['authors']) as $_key => $author) {
                                $user_name = User::getUserNameByID($author);
                                echo ($_key > 0 ? ', ' : '') . $user_name['user_name'];
                            }?>
                        </p>
                    <?php endif;?>
                </div>

                <?php if($training['show_desc'] && $training['short_desc']):?>
                    <div class="course_desc"><?=html_entity_decode($training['short_desc']);?></div>
                <?php endif;

                if(!empty($training['cover'])):?>
                    <div class="course_cover">
                        <img src="/images/training/<?=$training['cover'];?>" alt="<?=$training['img_alt'];?>"<?php if($training['padding']) echo ' style="padding: '.$training['padding'].';"';?>>
                    </div>
                <?php endif;

                if($user_id && $training['show_progress2list']): // получение пройденых уроков юзера
                    require (__DIR__ . '/../../../layouts/progressbar2list.php');?>
                <?php endif;?>

                <div class="course_bottom">
                    <div class="course_data_wrap">
                        <ul class="course_data">
                            <?php if ($training['duration_type'] == 2):?>
                                <li><?=System::Lang('TIME');?><br><?=$training['duration']?></li>
                            <?php elseif ($training['duration_type'] == 1 && $training['duration'] > 0):?>
                                <li><?=System::Lang('TIME');?><br><?=Training::countDurationByTraining($training);?></li>
                            <?php endif;

                            if($training['show_count_lessons']):?>
                                <li><?=System::Lang('LESSONS_FOR');?><br><?=TrainingLesson::getCountLessons2Training($training);?></li>
                            <?php endif;

                            if ($training['show_complexity'] == 1):?>
                                <?php if ($training['complexity'] == 1):?>
                                    <?=System::Lang('LIGHT_LEVEL');?>
                                <?php elseif ($training['complexity'] == 2):?>
                                    <?=System::Lang('AVERIGE_LEVEL');?>
                                <?php elseif ($training['complexity'] == 3):?>
                                    <?=System::Lang('COMPLEX_LEVEL');?>
                                <?php endif;?>
                            <?php endif;

                            if(isset($training['sertificate'])):
                                $show_sert = json_decode($training['sertificate'],true);
                                if (isset($show_sert['show_sert']) && $show_sert['show_sert']==1):?>
                                <li><i class="img-book"></i><?=System::Lang('CERTIFICAT_AVAILABLE');?></li>
                            <?php endif;
                            endif;

                            if($training['show_start_date'] == 1):?>
                                <li><?=System::Lang('START');?><br><?=($now < $training['start_date'] ? date("d.m.Y H:i:s", $training['start_date']) : System::Lang('ANY_TIME'));?></li>
                            <?php endif;?>
                        </ul>
                    </div>

                    <div class="course_links">
                        <div class="course_readmore">
                            <?php if($buttons['big_button']):?>
                                <div class="z-1">
                                    <?php if(isset($buttons['over_button_text'])):?>
                                        <p class="small"><?=$buttons['over_button_text'];?></p>
                                    <?php endif;

                                    if(mb_stripos($buttons['big_button']['url'], "?viewmodal")):?>
                                        <a data-uk-lightbox="" data-lightbox-type="iframe" class="<?=Training::getCssClasses($setting, $buttons['big_button']['class-type']);?>" href="<?=$buttons['big_button']['url'];?>">
                                            <?=$buttons['big_button']['text'];?>
                                        </a>
                                    <?php else:?>
                                        <a class="<?=Training::getCssClasses($setting, $buttons['big_button']['class-type']);?>" href="<?=$buttons['big_button']['url'];?>">
                                            <?=$buttons['big_button']['text'];?>
                                        </a>
                                    <?php endif;?>
                                </div>
                            <?php endif;

                            if(isset($buttons['small_button']) && $buttons['small_button']):?>
                                <div class="z-1">
                                    <?php if(mb_stripos($buttons['small_button']['url'], "?viewmodal")):?>
                                        <a data-uk-lightbox="" data-lightbox-type="iframe" class="<?=Training::getCssClasses($setting, $buttons['small_button']['class-type']);?>" href="<?=$buttons['small_button']['url'];?>">
                                            <?=$buttons['small_button']['text'];?>
                                        </a>
                                    <?php else:?>
                                        <a class="<?=Training::getCssClasses($setting, $buttons['small_button']['class-type']);?>" href="<?=$buttons['small_button']['url'];?>">
                                            <?=$buttons['small_button']['text'];?>
                                        </a>
                                    <?php endif;?>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(++$tr_index % 3 == 0 && count($training_list) != $tr_index):?>
                <div class="course_line"></div>
            <?php endif;
        endforeach;
    endif;?>
</div>
