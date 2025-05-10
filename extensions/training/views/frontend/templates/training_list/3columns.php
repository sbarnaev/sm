<?php  defined('BILLINGMASTER') or die;?>

<?php if ((isset($this->tr_settings['filter']) || isset($widget_params['params']['filter'])) && $is_page == 'training_index') {
    require_once(__DIR__ . '/../../filter/filter.php');
}
$setting = isset($this->setting) ? $this->setting : System::getSetting();?>

<div class="row course_list" data-uk-grid-match=" { target:'.course_cover' } ">
    <?php if ($training_list):
        $tr_index = 0;
        foreach($training_list as $key => $training):
            if (!$training['show_in_main']) {
                continue;
            };

            $access = Training::getAccessData($user_groups, $user_planes, $training);
            $buttons = Training::renderByButtons($access, $training);?>

            <div class="col-1-3 course_item col-1-3__trening-2">
                <div class="course_item__top <?php if(empty($training['cover'])):?>course_item__top--mt-25<?php endif;?>">
                    <h2 class="course_item__title"><?=$training['name'];?></h2>
                    <?php if($training['show_price']):?>
                        <span class="course_item__price"><?=!empty($training['price']) ? "{$training['price']}" : 'Бесплатно';?></span>
                    <?php endif;

                    if(!empty($training['authors'])):?>
                    <p class="course_author">Автор:
                        <?php foreach(explode(',', $training['authors']) as $_key => $author) {
                            $user_name = User::getUserNameByID($author);
                            echo ($_key > 0 ? ', ' : '') . $user_name['user_name'];
                        }?>
                    </p>
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
                    require (__DIR__ . '/../../layouts/progressbar2list.php');?>
                <?php endif;?>

                <div class="course_bottom">
                    <div class="course_data_wrap">
                        <ul class="course_data">
                            <?php if ($training['duration_type'] == 2):?>
                                <li>Время:<br><?=$training['duration']?></li>
                            <?php elseif ($training['duration_type'] == 1 && $training['duration'] > 0):?>
                                <li>Время:<br><?=Training::countDurationByTraining($training);?></li>
                            <?php endif; ?>

                            <?php if($training['show_count_lessons']):?>
                                <li>Уроков:<br><?=TrainingLesson::getCountLessons2Training($training);?></li>
                            <?php endif;?>

                            <?php if ($training['show_complexity'] == 1):?>
                                <?php if ($training['complexity'] == 1):?>
                                    <li>Уровень:<br>Легкий</li>
                                <?php elseif ($training['complexity'] == 2):?>
                                    <li>Уровень:<br>Средний</li>
                                <?php elseif ($training['complexity'] == 3):?>
                                    <li>Уровень:<br>Сложный</li>
                                <?php endif;?>            
                            <?php endif;?>

                            <? /*
                            <?php if($training['show_hits']):?>
                                <li>Просмотров:<br><?=Training::countHitsByTraining($training['training_id']);?></li>
                            <?php endif;?>
                            */ ?>

                            <?php if($training['sertificate_id']):?>
                                <li>Сертификат<br>Доступен</li>
                            <?php endif;?>

                            <?php if($training['show_start_date'] == 1):?>
                                <li>Старт:<br><?=($now < $training['start_date'] ? date("d.m.Y H:i:s", $training['start_date']) : 'любое время');?></li>
                            <?php endif;?>
                        </ul>
                    </div>

                    <div class="course_links">
                        <div class="course_readmore">
                            <?php if($buttons['big_button']):?>
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

            <?php if (++$tr_index % 3 == 0 && count($training_list) != $tr_index):?>
                <div class="course_line"></div>
            <?php endif;?>
        <?php endforeach;
    endif;?>
</div>