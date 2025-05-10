<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
$now = time();?>

<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>
    
    
    <div id="content">
        <div class="layout" id="courses">
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <?php // Вывод промо кода
                    require_once (__DIR__ . '/../common/show_promo_code.php');

                    // Вывод уведомления CallPassword
                    if(CallPassword::isShowButton($user)) {
                        require_once (ROOT . '/extensions/callpassword/views/show_notice.php');
                    }

                    // Вывод уведомления Telegram
                    if (Telegram::isShowButton($user['user_id'], $user['nick_telegram'])) {
                        require_once (ROOT . '/extensions/telegram/views/show_notice.php');
                    }?>

                    <h1 class="cource-head"><?=System::Lang('MY_COURSES');?></h1>

                    <?php $user_groups = false;
                    $user_planes = false;
                    if($user_id):
                        $user_id = intval($user_id);
                        $user_groups = User::getGroupByUser($user_id); // Получить группы юзера
                        $membership = System::CheckExtensension('membership', 1); // Получить подписки юзера, если установлен membership

                        if ($membership) {
                            $user_planes = Member::getPlanesByUser($user_id);
                        }

                        $course_list = Course::getAllCourseList(0); // Получить все тренинги
                        if($course_list):?>
                            <div class="row course_list" data-uk-grid-match=" { target:'.course_cover' } ">
                                <?php foreach ($course_list as $course): // Перебор курсов
                                    $i = 0;
                                    $access = false;
                                    $course_id = $course['course_id'];

                                    if ($course['access_type'] == 1 && $user_groups) {
                                        $course_groups = unserialize($course['groups']);
                                        if ($course_groups) {
                                            foreach($user_groups as $group){
                                                if (in_array($group, $course_groups)) {
                                                    $access = true;
                                                }
                                            }
                                        }
                                    } elseif($course['access_type'] == 2 && $user_planes && $course['access']) {
                                        $course_planes = unserialize($course['access']);
                                        foreach ($user_planes as $plane) {
                                            if (in_array($plane['subs_id'], $course_planes)) {
                                                $access = true;
                                            }
                                        }
                                    }

                                    if ($access):?>
                                        <div class="col-1-3 course_item">
                                            <?php // прогресс бар
                                            $amount = Course::countLessonByCourse($course_id);
                                            $map_items_progress = Course::getCompleteLessonsUser($user_id, $course_id, 1);
                                            $completed = $map_items_progress > 0 ? count($map_items_progress) : 0;
                                            $progress = $amount != 0 ? ($completed / $amount) * 100 : 0;

                                            if(!empty($course['cover'])):?>
                                                <div class="course_cover">
                                                    <a href="/courses/<?=$course['alias'];?>">
                                                        <img src="/images/course/<?=$course['cover'];?>" alt="<?=$course['img_alt'];?>"<?php if(!empty($course['padding'])) echo " style=\"padding: {$course['padding']};\"";?>>
                                                    </a>
                                                </div>
                                            <?php endif;?>

                                            <div class="course_item__middle">
                                                <h4 class="course_item__title"><?=Course::getCourseNameByID($course_id);?></h4>
                                                <?php if(!empty($course['author_id'])):
                                                    $user_name = User::getUserNameByID($course['author_id']);?>
                                                    <p class="course_author"><?=System::Lang('AUTHOR');?> <?=$user_name['user_name'];?></p>
                                                <?php endif;?>
                                                <div class="course_desc"><?php if($course['show_desc'] == 1) echo $course['short_desc'];?></div>
                                            </div>

                                            <div class="course_links">
                                                <div class="course_readmore">
                                                    <a class="btn-yellow" href="/courses/<?=$course['alias'];?>"><?=System::Lang('GO_TO_REVIEW');?></a>
                                                </div>

                                                <div class="progress_course">
                                                    <?php $duration = Course::countDurationByCourse($course['course_id']);
                                                    if($course['show_progress'] == 1):?>
                                                        <div class="progress_bar">
                                                            <div class="completed_line" style="width:<?=ceil($progress);?>%"> </div>
                                                        </div>

                                                        <div class="progress-row">
                                                            <div class="progress-left"><?=ceil($progress);?><?=System::Lang('PERCENTAGE_PASSED');?></div>
                                                            <div class="progress-right"><?=Course::countLessonByCourse($course['course_id']);?> <?=System::Lang('FOR_LESSONS');?></div>
                                                        </div>
                                                    <?php endif;?>
                                                </div>
                                            </div>

                                            <div class="course_data_wrap">
                                                <ul class="course_data old_course_data">
                                                    <?php if($duration):?>
                                                        <li><?=System::Lang('TIME');?><br><?=$duration;?> <?=System::Lang('MINUTES');?></li>
                                                    <?php endif; ?>

                                                    <?php if($course['show_lessons_count'] == 1):?>
                                                        <li class="text-center"><?=System::Lang('LESSONS_FOR');?><br><?=Course::countLessonByCourse($course['course_id']);?></li>
                                                    <?php endif;?>

                                                    <?php if($course['show_hits'] == 1):?>
                                                        <li><nobr><?=System::Lang('VIEWS');?></nobr><br><?=Course::countHitsByCourse($course['course_id']);?></li>
                                                    <?php endif;?>

                                                    <?php if($course['sertificate_id'] != 0):?>
                                                        <li><?=System::Lang('CERTIFICAT_AVAILABLE');?></li>
                                                    <?php endif;?>

                                                    <li><?=$course['is_free'] == 1 ? 'Доступ:<br>Бесплатно' : 'Доступ:<br>Платно';?></li>

                                                    <?php if($course['show_begin'] == 1):?>
                                                        <li class="text-center"><?=System::Lang('START');?><br>
                                                            <?=($now < $course['start_date'] ? date("d.m.Y H:i", $course['start_date']) : 'любое время');?>
                                                        </li>
                                                    <?php endif;?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif;
                                endforeach;?>
                            </div>
                        <?php endif;
                    else:?>
                        <p><strong><?=System::Lang('NOT_LOGING');?></strong></p>
                        <p><a href="#ModalEnter"><?=System::Lang('SITE_LOGIN');?></a></p>
                    <?php endif;?>
                </div>
			
                <?php if($sidebar):?>
                    <aside class="sidebar">
                        <?php $widget_arr = $sidebar;
                        require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php');?>
                    </aside>
                <?php endif;?>
            </div>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>