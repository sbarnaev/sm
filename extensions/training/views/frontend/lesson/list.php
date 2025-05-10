<?php defined('BILLINGMASTER') or die;?>

<div class="lessons_list">
    <?php $access_last_homework = true;
    foreach($lesson_list as $key => $lesson):
        if (!isset($block) && $lesson['block_id'] != 0 || isset($block) && $lesson['block_id'] != $block['block_id']) {
            continue;
        }
        // TODO SM- пока костыль (( ждем спринт по рефакторингу 
        if (isset($lesson['end_date']) && !empty($lesson['end_date'])){
            if ($lesson['end_date']<time()) {
                continue;
            }
        }

        $section = TrainingSection::getSection($lesson['section_id']);

        // Смотрим есть ли в прошлом стоповый урок
        $has_lesson_last_stop = TrainingLesson::isLessonLastStopStatus($lesson['sort'], $training['training_id']);
        if ($has_lesson_last_stop) {
            $access_last_homework = TrainingLesson::isAccessLastHomework($has_lesson_last_stop, $training, $lesson, $section, $user_id, $user_groups, $user_planes);
        }

        $lesson_status = Traininglesson::getLessonStatus($user_groups, $user_planes, $training, $section, $lesson, $user_id, $access_last_homework);
        $lesson_status_data = Traininglesson::getLessonStatusData($lesson_status, $training, $lesson);?>
            <?php if($lesson_status != 5 && $lesson_status != 8 || $lesson['shedule_hidden'] == 2):?>
                <div class="lesson_item just-lesson <?php if(empty($lesson['cover'])):?>just-lesson-not-image<?php endif;?> <?php if(!empty($lesson['cover'])):?>just-lesson-with-image<?php endif;?>">
                    <span class="lesson_number"><?=($lesson['sort']);?></span>
                    <?php if(!empty($lesson['cover'])):?>  
                        <a href="<?=$lesson_status_data['link'];?>" data-lesson_id="<?=$lesson['lesson_id'];?>">
                            <div class="lesson_cover">
                            <?php 
                                $pathfileimage = $_SERVER['DOCUMENT_ROOT'].'/images/training/lessons/'.$lesson['cover'];
                                $pathinfoimage = pathinfo($pathfileimage);
                                $resizefile = $pathinfoimage['filename'].'-'.$this->tr_settings['width_less_img'].'px.'.$pathinfoimage['extension'];
                                if (file_exists($pathinfoimage['dirname'].'/'.$resizefile)):?>
                                    <?php if($detect->isMobile()){?>
                                        <?php if($training['show_lesson_cover_2mobile'] == 1){?>
                                        <img src="/images/training/lessons/<?=$resizefile;?>" alt="<?=$lesson['img_alt'];?>"/>
                                        <?php }?>
                                    <?php } else {?>
                                        <img src="/images/training/lessons/<?=$resizefile;?>" alt="<?=$lesson['img_alt'];?>"/>
                                    <?php }?>
                        
                                <?php elseif (file_exists($pathfileimage)):
                                    $newimage = $pathinfoimage['dirname'].'/'.$resizefile;
                                    if (copy($pathfileimage, $newimage)):
                                        $resize = System::imgResize($newimage, 600, false);?>
                                        <?php if($detect->isMobile()){?>
                                            <?php if($training['show_lesson_cover_2mobile'] == 1){?>
                                            <img src="/images/training/lessons/<?=$resizefile;?>" alt="<?=$lesson['img_alt'];?>"/>
                                            <?php }?>
                                        <?php } else {?>
                                            <img src="/images/training/lessons/<?=$resizefile;?>" alt="<?=$lesson['img_alt'];?>"/>
                                        <?php }?>
                                    <?php endif;
                                endif;?>
                            </div>
                        </a>
                    <?php endif;?>

                    <div class="lesson_desc">
                        <div class="<?=$lesson_status_data['class'];?>">
                            <a href="<?=$lesson_status_data['link'];?>" data-lesson_id="<?=$lesson['lesson_id'];?>"><?=$lesson['name'];?></a>
                            <div class="lesson_desc-inner"><?=html_entity_decode($lesson['less_desc']);?></div>
                            <div class="lesson-info">
                                <div class="lesson-title-status"><?=$lesson_status_data['html'];?></div>
                                <?php if($lesson['duration'] > 0):?>
                                    <div class="lesson-duration"><?=System::Lang('DURATION');?> <?=$lesson['duration'];?>&nbsp<?=System::Lang('MINUTES');?></div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;
    endforeach;?>
</div>