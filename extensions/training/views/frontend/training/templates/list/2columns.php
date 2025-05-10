<?php defined('BILLINGMASTER') or die;

if ((isset($this->tr_settings['filter']) || isset($widget_params['params']['filter'])) && $is_page != 'my_trainings') {
    require_once(__DIR__ . '/../../../filter/filter.php');
    $training_filter_enabled = true;
}?>

<div class="row row-2-column training_list" data-uk-grid-match=" { target:'.course_cover' } ">
    <?php if($training_list):
        $setting = isset($this->setting) ? $this->setting : System::getSetting();
        $tr_index = 0;
        foreach($training_list as $training):
            if (!$training['show_in_main'] && $current_url !== "lk/mytrainings") {
                continue;
            };

            $access = Training::getAccessData($user_groups, $user_planes, $training);
            $buttons = Training::renderByButtons($access, $training);?>

            <div class="col-1-2 course_item col-1-2__training-2">
                <div class="course_item__top">
                    <div class="course_item__top-inner">
                        <h2 class="course_item__title"><?=$training['name'];?></h2>
                        <?php if(!empty($training['authors'])):?>
                            <p class="course_author"><?=System::Lang('AUTHOR');?>
                                <?php foreach(explode(',', $training['authors']) as $key => $author) {
                                    $user_name = User::getUserNameByID($author);
                                    echo ($key > 0 ? ', ' : '') . $user_name['user_name'];
                                }?>
                            </p>
                        <?php endif;?>
                    </div>

                    <?php if($training['show_price']):?>
                        <span class="course_item__price"><?=!empty($training['price']) ? "{$training['price']}" : System::Lang('FREE');?></span>
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
                    require (__DIR__ . '/../../../layouts/progressbar2list.php');
                endif;?>

                <div class="course_bottom">
                    <div class="course_data_wrap course_data__2-col">
                        <ul class="course_data">
                            <?php if ($training['duration_type'] == 2):?>
                                <li><i class="img-date icon-hourglass"></i><?=$training['duration']?></li>
                            <?php elseif ($training['duration_type'] == 1 && $training['duration'] > 0):?>
                                <li><i class="img-date icon-hourglass"></i><?=Training::countDurationByTraining($training);?></li>
                            <?php endif;

                            if($training['show_count_lessons']):?>
                                <li><i class="img-book icon-kol-vo"></i><?=TrainingLesson::getCountLessons2Training($training);?> <?=System::Lang('FOR_LESSONS');?></li>
                            <?php endif;
                            if(isset($training['sertificate'])):
                                $show_sert = json_decode($training['sertificate'],true);
                                if (isset($show_sert['show_sert']) && $show_sert['show_sert']==1):?>
                                <li><i class="img-book"></i><?=System::Lang('CERTIFICAT_AVAILABLE');?></li>
                            <?php endif;
                            endif;
                            if ($training['show_complexity'] == 1):?>
                                <?php if ($training['complexity'] == 1):?>
                                    <li><i class="img-level icon-level"></i><?=System::Lang('LIGHT_WEIGHT');?></li>
                                <?php elseif ($training['complexity'] == 2):?>
                                    <li><i class="img-level icon-level"></i><?=System::Lang('AVERIGE');?></li>
                                <?php elseif ($training['complexity'] == 3):?>
                                    <li><i class="img-level icon-level"></i><?=System::Lang('COMPLEX');?></li>
                                <?php endif;
                            endif;

                            if($training['show_start_date']):?>
                                <li><i class="img-date icon-clock"></i><?=($now < $training['start_date'] ? date("d.m.Y", $training['start_date']) : System::Lang('ANY_TIME'));?></li>
                            <?php endif;?>
                        </ul>
                    </div>

                    <div class="course_links">
                        <div class="course_readmore">
                            <?php if ($buttons['big_button']):?>
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

            <?php if(++$tr_index % 2 == 0 && count($training_list) != $tr_index):?>
                <div class="course_line"></div>
            <?php endif;
        endforeach;
    endif;?>
</div>
