<?php  defined('BILLINGMASTER') or die;?>

<?php if ((isset($this->tr_settings['filter']) || isset($widget_params['params']['filter'])) && $is_page == 'training_index') {
    require_once(__DIR__ . '/../../filter/filter.php');
}
$setting = isset($this->setting) ? $this->setting : System::getSetting();?>

<div class="row row-2-column course_list" data-uk-grid-match=" { target:'.course_cover' } ">
    <?php if ($training_list):
        $tr_index = 0;
        foreach($training_list as $training):
            if (!$training['show_in_main']) {
                continue;
            };

            $access = Training::getAccessData($user_groups, $user_planes, $training);
            $buttons = Training::renderByButtons($access, $training);?>

            <div class="col-1-2 course_item col-1-2__trening-2">
                <div class="course_item__top">
                    <div class="course_item__top-inner">
                        <h2 class="course_item__title"><?=$training['name'];?></h2>
                        <?php if(!empty($training['authors'])):?>
                            <p class="course_author">Автор:
                                <?php foreach(explode(',', $training['authors']) as $key => $author) {
                                    $user_name = User::getUserNameByID($author);
                                    echo ($key > 0 ? ', ' : '') . $user_name['user_name'];
                                }?>
                            </p>
                        <?php endif;?>
                    </div>

                    <?php if($training['show_price']):?>
                        <span class="course_item__price"><?=!empty($training['price']) ? "{$training['price']}" : 'Бесплатно';?></span>
                    <?php endif;?>
                </div>


                <?php if($training['show_desc']):?>
                    <div class="course_desc"><?=$training['short_desc'];?></div>
                <?php endif;?>

                <?php if(!empty($training['cover'])):?>
                    <div class="course_cover">
                        <img src="/images/training/<?=$training['cover'];?>" alt="<?=$training['img_alt'];?>"<?php if($training['padding']) echo ' style="padding: '.$training['padding'].'"';?>>
                    </div>
                <?php endif;?>

                <?php if($user_id && $training['show_progress2list']): // получение пройденых уроков юзера
                    require (__DIR__ . '/../../layouts/progressbar2list.php');
                endif;?>

                <div class="course_bottom">
                    <div class="course_data_wrap course_data__2-col">
                        <ul class="course_data">
                            <?php if ($training['duration_type'] == 2):?>
                                <li><i class="img-date icon-hourglass"></i><?=$training['duration']?></li>
                            <?php elseif ($training['duration_type'] == 1 && $training['duration'] > 0):?>
                                <li><i class="img-date icon-hourglass"></i><?=Training::countDurationByTraining($training);?></li>
                            <?php endif;?>

                            <?php if($training['show_count_lessons']):?>
                                <li><i class="img-book icon-kol-vo"></i><?=TrainingLesson::getCountLessons2Training($training);?> уроков</li>
                            <?php endif;?>

                            <?php if($training['sertificate_id']):?>
                                <li><i class="img-book"></i>Доступен сертификат</li>
                            <?php endif;?>

                            <?php if ($training['show_complexity'] == 1):?>
                                <?php if ($training['complexity'] == 1):?>
                                    <li><i class="img-level icon-level"></i>Легкий</li>
                                <?php elseif ($training['complexity'] == 2):?>
                                    <li><i class="img-level icon-level"></i>Средний</li>
                                <?php elseif ($training['complexity'] == 3):?>
                                    <li><i class="img-level icon-level"></i>Сложный</li>
                                <?php endif;
                            endif;?>
                            

                            <?php if($training['show_start_date']):?>
                                <li><i class="img-date icon-clock"></i><?=($now < $training['start_date'] ? date("d.m.Y", $training['start_date']) : 'любое время');?></li>
                            <?php endif;?>
                            <? /*
                            <?php if($training['show_hits']):?>
                            <li><i class="img-hits icon-check"></i><?=Training::countHitsByTraining($training['training_id']);?></li>
                            <?php endif;?>
                            */ ?>
                        </ul>
                    </div>

                    <div class="course_links">
                        <div class="course_readmore">
                            <?php if ($buttons['big_button']):?>
                                <div class="z-1">
                                    <?php if(isset($buttons['over_button_text'])):?>
                                        <p class="small"><?=$buttons['over_button_text'];?></p>
                                    <?php endif;?>

                                    <a class="<?=Training::getCssClasses($setting, $buttons['big_button']['class-type']);?>" href="<?=$buttons['big_button']['url'];?>">
                                        <?=$buttons['big_button']['text'];?>
                                    </a>
                                </div>
                            <?php endif;

                            if(isset($buttons['small_button']) && $buttons['small_button']):?>
                                <div class="z-1">
                                    <a class="<?=Training::getCssClasses($setting, $buttons['small_button']['class-type']);?>" href="<?=$buttons['small_button']['url'];?>">
                                        <?=$buttons['small_button']['text'];?>
                                    </a>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (++$tr_index % 2 == 0 && count($training_list) != $tr_index):?>
                <div class="course_line"></div>
            <?php endif;
        endforeach;
    endif;?>
</div>