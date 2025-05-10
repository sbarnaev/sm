<?php defined('BILLINGMASTER') or die;
$now = time();
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>

    <style>
        .hero-wrap{
            min-height: 300px;
            height: <?=$params['params']['heroheigh'];?>px;
            background-position: <?=$params['params']['position']?>;
            background-size: cover;
        }
        .hero-wrap:before{
            opacity:<?=$params['params']['overlay']?>;
            background:<?=$params['params']['overlaycolor']?>;
        }

        .hero_header.h1 {color: <?=$params['params']['color']?>; font-size: <?=$params['params']['fontsize']?>px; }

        @media screen and (max-width: 640px),
        only screen and (max-device-width:640px) {
            .hero-wrap {height: <?=$params['params']['heromobileheigh'];?>px}
            .hero_header.h1 {font-size: <?=$params['params']['fontsize_mobile'];?>px}
        }
    </style>

    <?php
    $user_groups = false;
    $user_planes = false;
    if ($user_id) {
        // Получили группы юзера
        $user_groups = User::getGroupByUser($user_id);

        // Получить подписки юзера, если установлен membership
        $membership = System::CheckExtensension('membership', 1);
        if ($membership) {
            $user_planes = Member::getPlanesByUser($user_id);
        }  else {
            $user_planes = false;
        }
    }?>

    <div id="content">
        <?php if(isset($params['params']['hero']) && $params['params']['hero'] != null ):?>
            <div id="hero" class="hero-wrap old-hero-wrap" style="background-image: url(<?=$params['params']['hero'];?>)">
                <h1 class="layout hero_header h1"><?=$params['params']['heroheader'];?></h1>
            </div>
        <?php endif;?>

        <div class="layout" id="courses">
            <div class="content-courses">
                <div class="maincol<?php if($sidebar) echo '_min content-with-sidebar';?>">
                    <?php if(!empty($h1)):?>
                        <h1><?=$h1;?></h1>
                    <?php endif;?>

                    <?php // Категории курсов, если есть
                    if($cats):?>
                        <div class="course_category">
                            <h2><?=System::Lang('COURSE_CATEGORY');?> </h2>
                            <div class="row course_category__row" data-uk-grid-match=" { target:'.category_cover' } ">
                            <?php foreach($cats as $cat):?>
                                <div class="col-1-3 course_category_item">
                                    <div class="category_cover">
                                        <?php if(!empty($cat['cover'])):?>
                                            <a href="/courses?category=<?=$cat['alias'];?>">
                                                <img src="/images/course/category/<?=$cat['cover'];?>" alt="<?=$cat['img_alt'];?>">
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <div class="category_desc">
                                        <h3 class="category_desc__title"><a href="/courses?category=<?=$cat['alias'];?>"><?=$cat['name'];?></a></h3>
                                        <div class="course_count"><?=System::Lang('FOR_COURSES');?> <?=Course::countCourseinCategory($cat['cat_id'], 1);?></div>
                                        <?=$cat['cat_desc'];?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php // Тренинги
                    if($cat_name):?>
                        <h2><?=System::Lang('CATEGORY').$cat_name;?></h2>
                    <?php endif;

                    if($courses):?>
                        <div class="row course_list" data-uk-grid-match=" { target:'.course_cover' } ">
                            <?php foreach($courses as $course): // ПЕРЕБИРАЕМ ТРЕНИНГИ
                                $groups_arr = array();
                                $access = false;
                                if ($course['show_in_main'] == 0) {
                                    continue;
                                }?>

                                <div class="col-1-3 course_item">
                                    <?php if(!empty($course['cover'])):?>
                                        <div class="course_cover">
                                            <img src="/images/course/<?=$course['cover'];?>" alt="<?=$course['img_alt'];?>"<?php if(!empty($course['padding'])):?> style="padding: <?=$course['padding']?>;"<?php endif;?>>
                                        </div>
                                    <?php endif;?>

                                    <div class="course_item__middle">
                                        <h4 class="course_item__title"><?=$course['name'];?></h4>

                                        <?php if(!empty($course['author_id'])):?>
                                            <p class="course_author"><?=System::Lang('AUTHOR');?> <?php $user_name = User::getUserNameByID($course['author_id']); echo $user_name['user_name'];?></p>
                                        <?php endif;?>

                                        <?php if($course['show_desc'] == 1):?>
                                            <div class="course_desc"><?=$course['short_desc'];?></div>
                                        <?php endif;?>
                                    </div>

                                    <div class="course_links">
                                        <div class="course_readmore">
                                            <?php // Проверяем доступ к курсу
                                            $course_id = $course['course_id'];
                                            $data = Course::checkAccessCourse($course, $user_groups, $user_planes, $user_id);?>
                                            <a class="<?=$data['class_link'];?>" href="<?=$data['button_link'];?>"><?=$data['action']; ?></a>

                                            <?php if($data['text_link']):?>
                                                <div class="current-course-lp">
                                                    <a class="btn-link" href="<?=$data['text_link'];?>"><?=$data['text_link_anchor'];?></a>
                                                </div>
                                            <?php endif;?>
                                        </div>

                                        <?php $duration = Course::countDurationByCourse($course['course_id']);
                                        if($course['show_progress'] == 1 && $user_id):
                                            $map_items = Course::getCompleteLessonsUser($user_id, $course['course_id']);

                                            if($map_items):
                                                $amount = Course::countLessonByCourse($course['course_id']);
                                                $completed = count($map_items);
                                                $progress = ($completed / $amount) * 100;?>

                                                <div class="progress_course">
                                                    <div class="progress_bar">
                                                        <div class="completed_line" style="width:<?=ceil($progress);?>%<?php if($progress == 100) echo '; background: #4BD96A'?>"> </div>
                                                    </div>
                                                </div>

                                                <div class="progress-row">
                                                    <div class="progress-left"><?=ceil($progress);?><?=System::Lang('PERCENTAGE_PASSED');?></div>
                                                    <div class="progress-right">
                                                        <?=Course::countLessonByCourse($course['course_id']);?> <?=System::Lang('FOR_LESSONS');?>
                                                    </div>
                                                </div>
                                            <?php endif;
                                        endif;?>
                                    </div>

                                    <div class="course_data_wrap">
                                        <ul class="course_data old_course_data">
                                            <?php if(!empty($duration)):?>
                                                <li><?=System::Lang('TIME');?><br><?=$duration;?> <?=System::Lang('MINUTES');?></li>
                                            <?php endif;?>

                                            <?php if($course['show_lessons_count'] == 1):?>
                                                <li class="text-center"><?=System::Lang('LESSONS_FOR');?><br><?=Course::countLessonByCourse($course['course_id']);?></li>
                                            <?php endif;?>

                                            <?php if($course['show_hits'] == 1):?>
                                                <li><nobr><?=System::Lang('VIEWS');?></nobr><br><?=Course::countHitsByCourse($course['course_id']);?></li>
                                            <?php endif;?>

                                            <?php if($course['sertificate_id'] != 0):?>
                                                <li><?=System::Lang('CERTIFICAT_AVAILABLE');?></li>
                                            <?php endif;?>

                                            <li class="text-center"><?=System::Lang('ACCESS');?><br><?=($course['is_free'] == 1 ? 'Бесплатно' : 'Платно');?></li>

                                            <?php if($course['show_begin'] == 1):?>
                                                <li class="text-center"><?=System::Lang('START');?><br>
                                                    <?=($now < $course['start_date'] ? date("d.m.Y H:i", $course['start_date']) : 'любое время');?>
                                                </li>
                                            <?php endif;?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif;?>
                </div>
                <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>