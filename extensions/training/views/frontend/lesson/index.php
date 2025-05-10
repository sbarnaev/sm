<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/frontend/head.php');?>

<body id="page">
<?php require_once (ROOT . '/extensions/training/layouts/frontend/header.php');
require_once (ROOT . '/extensions/training/layouts/frontend/main_menu.php');?>
<script src="/template/<?=$this->setting['template'];?>/js/player_bm.js" type="text/javascript"></script>
<?php require_once (ROOT . '/extensions/training/web/frontend/style/lesson.php');?>

<div id="content">
    <!-- // здесь большая картинка с оверлеем если есть  -->
    <?php if($training['full_cover']):?>
        <div id="hero" class="hero-wrap" style="background-image: url(/images/training/<?=$training['full_cover']?>)">
            <div class="h1"><?=$training['name'];?></div>

            <ul class="breadcrumbs">
                <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section, $lesson);
                foreach ($breadcrumbs as $link => $name):?>
                    <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                <?php endforeach;?>
            </ul>
        </div>
    <?php endif;?>

    <div class="layout" id="courses">
        <?php if(!$training['full_cover']):?>
            <ul class="breadcrumbs">
                <?php $breadcrumbs = Training::getBreadcrumbs($this->tr_settings, $category, $sub_category, $training, $section, $lesson);
                foreach ($breadcrumbs as $link => $name):?>
                    <li><?=$link ? "<a href=\"$link\">$name</a>" : $name;?></li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>

        <?php // Ссылка на след. урок для автотренингов
        $access_homework = TrainingLesson::checkUserAccessHomeWork($user_groups, $user_planes, $training, $task);
        $lesson_is_stop = TrainingLesson::isLessonStopStatus($lesson['lesson_id']);
        $lesson_complete = TrainingLesson::isLessonComplete($lesson['lesson_id'], $user_id);
        $prev = $lesson['sort'] > 1 ? TrainingLesson::getLessonBySort($lesson['training_id'], $lesson['sort']-1, 1, 1) : null;
        $prev_link = $prev ? "/training/view/{$training['alias']}/lesson/{$prev['alias']}" : false;
        $next = TrainingLesson::getLessonBySort($lesson['training_id'], $lesson['sort'] + 1, 1, 2);
        $next_status = Training::getAccessData($user_groups, $user_planes, $training, $section, $next);
        $next_link = $next ? "/training/view/{$training['alias']}/lesson/{$next['alias']}" : false;
        if ($lesson_is_stop):
            $show_next_link = $lesson_complete && $next_status['status'] !== 7 ? true : false;
        else:
            $show_next_link = $next_status['status'] !== 7 ? true : false;
        endif;?>

        <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar<?php if($training['lessons_tmpl'] == 1) echo ' lesson-sidebar-outside';?>">
                <?php if(isset($_GET['success'])):?>
                    <div class="success_message"><?=System::Lang('USER_SUCCESS_MESS');?></div>
                <?php endif;?>
                
                <div class="lesson-inner">
                    <div class="lesson-inner-top ">
                        <h1 class="lesson-inner-h1 h2"><?=$lesson['name'];?></h1>

                        <div class="next_less_top-wrap<?php if(!$prev_link) echo ' not-prev_link';?>">
                            <?php if($prev_link):?>
                                <a class="next_less_top next_less_prev" href="<?=$prev_link;?>"><span><?=System::Lang('PREVIOUS_LESSON');?></span></a>
                            <?endif;

                            if($next_link):?>
                                <a class="next_less_top next_less_next<?php if(!$show_next_link) echo ' hidden';?>" href="<?=$next_link;?>"><span><?=System::Lang('NEXT_LESSON');?></span></a>
                            <?php endif;

                            if($lesson['show_comments'] && $task['task_type'] != 0 && in_array($lesson_homework_status, [TrainingLesson::LESSON_STARTED, TrainingLesson::HOMEWORK_DECLINE]) && $access_homework):?>
                                <span class="z-1">
                                    <a class="scroll-link btn-orange" href="#comments"><?=System::Lang('MAKE_HOME_TASK');?></a>
                                </span>
                            <?php endif;?>
                        </div>
                    </div>

                    <?php /*ЭЛЕМЕНТЫ УРОКА*/
                    require_once(__DIR__ . '/elements/index.php');

                    /*ДОМАШНЕЕ ЗАДАНИЕ*/
                    if ($user_id) {
                         require_once(__DIR__ . '/home_task.php');
                    }?>
                </div>

                <?php if($lesson['show_comments'] && $this->tr_settings['commentcode']):?>
                    <div class="block-border-top">
                        <div class="comments" id="comments">
                            <?=$this->tr_settings['commentcode'];?>
                        </div>
                    </div>
                <?php endif;?>

                <div class="next_less_top-wrap next_less_top-wrap--bottom<?php if(!$prev_link) echo ' not-prev_link';?>">
                    <?php if($prev_link):?>
                        <a class="next_less_top next_less_prev" href="<?=$prev_link;?>"><span><?=System::Lang('PREVIOUS_LESSON');?></span></a>
                    <?endif;

                    if($next_link):?>
                        <a class="next_less_top next_less_next<?php if(!$show_next_link) echo ' hidden';?>" href="<?=$next_link;?>"><span><?=System::Lang('NEXT_LESSON');?></span></a>
                    <?php endif;?>
                </div>
            </div>

            <!-- Здесь просто по PHP условию выводим блок с сайдбаром или нет -->
            <?php if($training['lessons_tmpl'] == 1):?> <!-- Это макет узкий, а значит блок весь выводится -->
                <aside class="sidebar">
                    <!-- класс widget-sticky сделал на всякий случай, если надо, чтобы прогресс бар был плавающим. Если не нужен, можно убрать. -->
                    <?php if($user_id && $training['show_widget_progress']):?>
                        <section class="widget _instruction">
                            <?php if ($training['cover'] && !$training['full_cover']):?>
                                <div class="sidebar-image"><img src="/images/training/<?=$training['cover']?>"></div>
                                <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                                <h4 class="traninig-name"><?=$training['name']?></h4>
                            <?php endif;?>

                            <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                            <p class="progress-text"><?=System::Lang('TRACK_YOUR_TRAINING');?></p>

                            <?php require_once (__DIR__ . '/../layouts/progressbar.php');?>
                        </section>
                    <?php else:?>
                        <section class="widget _instruction">
                            <?php if ($training['cover'] && !$training['full_cover']):?>
                                <div>
                                    <div class="sidebar-image">
                                        <img src="/images/training/<?=$training['cover']?>">
                                    </div>
                                </div>

                                <!-- ЗДЕСЬ выводим название тренинга, если обложка маленькая -->
                                <h4 class="traninig-name"><?=$training['name']?></h4>
                            <?php endif;?>

                            <h3><?=System::Lang('YOUR_PROGRESS');?></h3>
                            <p><?=System::Lang('PROGRESS_OF_THE_TRAINING_WILL_BE_DISPLAYED_HERE');?></p>
                        </section>
                    <?php endif;

                    if($user_is_curator):?>
                        <section class="widget elephant-widget">
                            <h3 class="elephant-title"><i class="icon-elephant"></i><?=System::Lang('LOGIN_AS_CURATOR');?></h3>
                            <p><?=System::Lang('ALL_LESSONS_AVAILABLE');?></p>
                        </section>
                    <?php endif;

                    if($sidebar):
                        $widget_arr = $sidebar;
                        require(ROOT . '/template/'.$this->setting['template'].'/widgets/widget_wrapper.php');
                    endif;?>
                </aside>
            <?php endif;?>
        </div>
    </div>
</div>

<?php require_once (ROOT . '/extensions/training/layouts/frontend/footer.php');
require_once (ROOT . '/extensions/training/layouts/frontend/tech-footer.php');

$watermark = isset($user['email']) ? htmlentities($user['email']) : '';
if (isset($user['phone'])) {
    $watermark .= $user['phone'];
}

if($elements):
    foreach ($elements as $element):
        if($element['type'] == TrainingLesson::ELEMENT_TYPE_MEDIA && $element['params']['element_type'] == 2):?>
            <script>
                var player = new Playerjs({id:"player_<?=$element['id'];?>", file:window.atob("<?=base64_encode(trim($element['params']['url']));?>"), design:1, <?php if($watermark != false):?>wid:"<?=$watermark;?>",<?php endif;?> <?php if(isset($_GET['testmode'])):?>wid_test:1, <?php endif;?> poster:"<?=$element['params']['cover'];?>"});
            </script>
        <?php endif;
        
        if($element['type'] == TrainingLesson::ELEMENT_TYPE_MEDIA && $element['params']['element_type'] == 3):?>
            <script src="/template/<?=$setting['template'];?>/js/audio_play.js" type="text/javascript"></script>
            <script>
              var player = new Playerjs({id:"a_player_<?=$element['id'];?>", file:window.atob("<?=base64_encode(trim($element['params']['url'])); ?>"), design:1});
            </script>
        <?php endif;
    endforeach;
endif;?>

<script src="/template/<?=$this->setting['template'];?>/js/player_bm.js" type="text/javascript"></script>
</body>
</html>