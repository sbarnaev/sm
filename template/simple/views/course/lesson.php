<?php
defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>

<body id="page">
<?php
require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');
?>


<div id="content">
    <div class="layout" id="courses">
        <ul class="breadcrumbs">
            <li><a href="/"><?=System::Lang('MAIN');?></a></li>
            <li><a href="/courses"><?=System::Lang('ONLINE_COURSES');?></a></li>
            <li><a href="/courses/<?=$course['alias'];?>"><?=$course['name'];?></a></li>
            <li><?=$lesson['name'];?></li>
        </ul>

        <?php // Ссылка на след. урок для автотренингов
        $complete = false;
        $complete2 = false;
        $next_link = false;

        if (isset($map_items) && $map_items == true) {
            foreach($map_items as $item){
                if($lesson['lesson_id'] == $item['lesson_id']) {
                    $complete2 = true;
                    if($item['status'] == 1) $complete = 1;
                }
            }

            if ($complete2) {
                if($complete == false) $complete = 2;
            }
        }

        $prev = Course::getLessonByCount($lesson['course_id'], $lesson['sort']-1);
        $prev_link = $lesson['sort'] > 1 ? "/courses/{$course['alias']}/{$prev['alias']}" : false;


        // Получить максимальное значение count в этом курсе
        $max_num = Course::getMaxNumLesson($lesson['course_id']);


        // Если этот урок не равен МАХ count, значит показываем ссылку
        if ($lesson['sort'] != $max_num && ($course['auto_train'] != 1 || $complete == 1 || $complete == 3 || $lesson['task_type'] === '0')) {
            // Получаем из  БД след урок
            $next = Course::getLessonByCount($lesson['course_id'], $lesson['sort']+1);
            $next_link = '/courses/'.$course['alias'].'/'.$next['alias'];
        } else {
            $next_link = false;
        }?>

        <div class="content-wrap">
            <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                <div class="lesson-inner">

                    <div class="lesson-inner-top">
                        <h1 class="lesson-inner-h1"><?=$lesson['name'];?></h1>

                        <div class="next_less_top-wrap<?php if(!$prev_link) echo ' not-prev_link';?>">
                            <?php if($prev_link):?>
                                <a class="next_less_top" href="<?=$prev_link;?>"><?=System::Lang('PREVIOUS_LESSON');?></a>
                            <?endif;
                            if($next_link):?>
                                <a class="next_less_top" href="<?=$next_link;?>"><?=System::Lang('NEXT_LESSON');?></a>
                            <?endif;?>
                        </div>
                    </div>

                    <?php if(!empty($lesson['custom_code_up'])):?>
                        <div class="custom_code_up"><?=$lesson['custom_code_up'];?></div>
                    <?php endif;?>

                    <?php if(!empty($lesson['video_urls'])):
                        $count = 0;
                        $video_url = explode("\r\n", $lesson['video_urls']);
                        foreach($video_url as $url):
                            if (empty($url)) {
                                continue;
                            };?>

                            <div id="bm_player<?=$count++;?>" class="video_separator"></div>
                        <?php endforeach;
                    endif;?>


					<?php if(!empty($lesson['audio_urls'])):
                        $a_count = 0;
                        $audio_url = explode("\r\n", $lesson['audio_urls']);
                        foreach($audio_url as $a_url):
                            if(empty($a_url)) continue;?>

                            <div id="a_player<?=$a_count;?>" class="video_separator"></div>

                            <?php $a_count++;
                        endforeach;
                    endif;?>

                    <div class="lesson_content"><?=$lesson['content'];?></div>
                    <div class="custom_code"><?=$lesson['custom_code'];?></div>

                    <?php if (!empty($lesson['attach'])):?>
                        <div class="attach mt-5">
                            <div>
                                <span class="small"><?=System::Lang('ATTACHED_ATTACHMENTS');?></span>
                            </div>

                            <div>
                                <?php foreach(json_decode($lesson['attach'], true) as $attach_name):?>
                                    <a style="margin-right:10px;" target="_blank" href="/load/lessons/<?="{$lesson['lesson_id']}/$attach_name";?>">
                                        <nobr>
                                            <img src="/template/admin/images/attachment.png" alt="" style="width:16px;margin-right:5px;">
                                            <span><?=$attach_name;?></span>
                                        </nobr>
                                    </a><br>
                                <?php endforeach?>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php // ДОПМАТ
                    if($user):
                        if(!empty($lesson['dopmat'])):?>
                            <div class="dopmat_box">
                                <h2><?=System::Lang('DOPMAT');?></h2>
                                <ol>
                                    <?php $dopmat_arr = unserialize($lesson['dopmat']);
                                    foreach($dopmat_arr as $dopmat):
                                        $dopmat_link = Course::getDopmatLink($dopmat); // Получить ссылку на допмат
                                        if ($dopmat_link):?>
                                            <li><a href="/load/dopmat/<?=$dopmat_link['file'];?>" target="_blank"><?=$dopmat_link['name'];?></a></li>
                                        <?php endif;
                                    endforeach;?>
                                </ol>
                            </div>
                        <?php endif;?>

                        <?php // ЗАДАНИЕ
                        if($lesson['task_type'] != 0):?>
                            <div class="home-work">
                                <h4 class="home-work__title"><?=System::Lang('HOME_TASK');?></h4>
                                <?=$lesson['task'];?>

                                <?php if($messages):?>
                                    <div class="answer_list">
                                        <?php foreach($messages as $message):
                                            $user = User::getUserNameByID($message['user_id']);?>

                                            <div class="answer_item answer_question">
                                                <div class="answer_item__inner">
                                                    <div class="answer_item__left">
                                                        <img src="<?=User::getAvatarUrl($user, $setting);?>" alt="" />
                                                    </div>

                                                    <div class="answer_item__right">
                                                        <div class="user_name">
                                                            <p><?=$user['user_name'];?> <span class="user_name-id">(# <?=$message['id'];?>)</span></p>
                                                            <span class="small"><?=date("d.m.Y H:i:s",$message['date']);?></span>
                                                        </div>

                                                        <div class="user_message">
                                                            <?=$message['body'];?>
                                                        </div>

                                                        <?php if(!empty($message['attach'])):?>
                                                            <div class="attach mt-5">
                                                                <span class="small"><?=System::Lang('ATTACHED_ATTACHMENTS');?></span>
                                                                <ul style="list-style:none;margin-top:0;padding-left:0;">
                                                                    <?php foreach(json_decode($message['attach'], true) as $attach):?>
                                                                        <li><img style="width:16px;margin-right:5px;" src="/template/admin/images/attachment.png" alt=""><?=$attach['name'];?></li>
                                                                    <?php endforeach;?>
                                                                </ul>
                                                            </div>
                                                        <?endif?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php $answers = Course::getAnswerFromMess($message['id']);
                                            if($answers):
                                                foreach($answers as $answer):
                                                    $user = User::getUserNameByID($answer['user_id']);?>

                                                    <div class="answer_item answer">
                                                        <div class="answer_item__inner">
                                                            <div class="answer_item__left">
                                                                <img src="<?=User::getAvatarUrl($user, $setting);?>" alt="" />
                                                            </div>

                                                            <div class="answer_item__right">
                                                                <div class="user_name">
                                                                    <p><?php $curator = User::getUserNameByID($answer['user_id']); echo $curator['user_name'];?></p>
                                                                    <span class="small"># <?=$answer['id'];?><br /><?=date("d.m.Y H:i:s", $answer['date']);?></span>
                                                                </div>

                                                                <div class="user_message">
                                                                    <?=$answer['body'];?>
                                                                </div>

                                                                <?php if(!empty($answer['attach'])):?>
                                                                    <div class="attach mt-5">
                                                                        <span class="small"><?=System::Lang('ATTACHED_ATTACHMENTS');?></span>
                                                                        <ul style="list-style:none;margin-top:0;padding-left:0;">
                                                                            <?php foreach(json_decode($answer['attach'], true) as $attach):?>
                                                                                <li>
                                                                                    <a href="<?=urldecode($attach['path']);?>" target="_blank">
                                                                                        <img style="width:16px;margin-right:5px;" src="/template/admin/images/attachment.png" alt=""><?=$attach['name'];?>
                                                                                    </a>
                                                                                </li>
                                                                            <?php endforeach;?>
                                                                        </ul>
                                                                    </div>
                                                                <?endif?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach;
                                            endif;
                                        endforeach;?>
                                    </div>
                                <?php endif;

                                if($lesson['task_type'] == 0 || $lesson['task_type'] == 1):?>
                                    <?php if(!$complete):?>
                                        <form class="form-complete" action="" method="POST" id="complete">
                                            <input type="submit" name="complete" class="less_complete static" style="position:static" value="Отметить пройденным">
                                        </form>
                                    <?php else:?>
                                        <span class="less_complete">
                                            <span class="icon-check"></span><?=System::Lang('DONE');?>
                                        </span>

                                        <?php if($next_link):?>
                                            <div class="next_less__wrap">
                                                <a class="next_less btn-blue" href="<?=$next_link;?>"><?=System::Lang('NEXT_LESSON');?></a>
                                            </div>
                                        <?php endif;
                                    endif;

                                elseif($lesson['task_type'] == 2):
                                    if($complete):
                                        if($complete == 1):?>
                                            <span class="less_complete">
                                                <span class="icon-check"></span><?=System::Lang('ACCEPTED');?>
                                            </span>

                                            <?php if($next_link):?>
                                                <div class="next_less__wrap">
                                                    <a class="next_less btn-blue" href="<?=$next_link;?>"><?=System::Lang('NEXT_LESSON');?></a>
                                                </div>
                                            <?php endif;
                                        else:?>
                                            <span class="less_waiting"><span class="icon-stopwatch"></span> <?=System::Lang('ON_CHECK');?></span>
                                        <?php endif;?>
                                    <?php else:?>
                                        <!-- Если не нужен текстовый редактор, то убираем у textarea класс editor и добавляем класс no-editor -->
                                        <form enctype="multipart/form-data" class="form-complete" action="" method="POST" id="complete">
                                            <p class="form-complete-head"><?=System::Lang('ADD_ANSWER');?></p>

                                            <div class="textarea__row">
                                                <div class="textarea__left">
                                                    <textarea placeholder="Введите ответ" name="answer" class="editor"></textarea>
                                                    <div class="attach">
                                                        <label><?=System::Lang('ATTACH_FILES');?></label>
                                                        <input type="file" multiple name="lesson_attach[]">
                                                    </div>
                                                </div>

                                                <div class="textarea__righr">
                                                    <input type="submit" name="complete" class="button btn-blue-small" value="Отправить">
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif;

                                elseif($lesson['task_type'] == 3):
                                    if($complete):
                                        if($complete == 1):?>
                                            <span class="less_complete">
                                                <span class="icon-check"></span><?=System::Lang('ACCEPTED');?>
                                            </span>

                                            <?php if($next_link):?>
                                                <div class="next_less__wrap">
                                                    <a class="next_less btn-blue" href="<?=$next_link;?>"><?=System::Lang('NEXT_LESSON');?></a>
                                                </div>
                                            <?php endif;
                                        else:?>
                                            <span class="less_waiting">
                                                <span class="icon-stopwatch"></span><?=System::Lang('ON_CHECK');?>
                                            </span>
                                            <p class="less_waiting-line"><?=System::Lang('WAITING_FOR_ANSWER');?></p>
                                        <?php endif;
                                    endif;?>

                                    <form enctype="multipart/form-data" class="form-complete" action="" method="POST" id="complete">
                                        <p class="form-complete-head"><?=System::Lang('ADD_ANSWER');?></p>
                                        <div class="textarea__row">
                                            <div class="textarea__left">
                                                <textarea placeholder="Введите ответ" name="answer" class="editor"></textarea>
                                                <div class="attach">
                                                    <label><?=System::Lang('ATTACH_FILES');?></label>
                                                    <input type="file" multiple name="lesson_attach[]">
                                                </div>
                                            </div>

                                            <div class="textarea__righr">
                                                <input type="submit" name="complete" class="button btn-blue-small" value="Отправить">
                                            </div>
                                        </div>
                                    </form>
                                <?php endif?>
                            </div>
                        <?php endif;
                    endif;

                    if($lesson['show_comments'] == 1):?>
                        <div class="comments" id="comments">
                            <?=$params['params']['commentcode'];?>
                        </div>
                    <?php endif;?>
                </div>
            </div>



            <aside class="sidebar">
                <div class="current-course">
                    <div class="current-course-img">
                        <?php if(!empty($course['cover'])):?>
                            <img src="/images/course/<?=$course['cover'];?>" alt="<?=$course['img_alt'];?>"<?php if(!empty($course['padding'])) echo " style=\"padding:{$course['padding']};\"";?>>
                        <?php endif; ?>
                    </div>

                    <div class="current-course-inner">
                        <div class="current-course-top">
                            <h4 class="current-course-title"><?=$course['name'];?></h4>
                            <?php if(!empty($course['author_id'])):
                                $author_name = User::getUserNameByID($course['author_id']);?>
                                <p class="course_author"><?=System::Lang('AUTHOR');?> <?=$author_name['user_name'];?></p>
                            <?php endif;?>
                        </div>

                        <?php // Прогресс Бар
                        if($user && $course['show_progress'] == 1):
                            $amount = Course::countLessonByCourse($course['course_id']);
                            $completed = $map_items_progress > 0 ? count($map_items_progress) : 0;
                            $progress = $amount != 0 ? ($completed / $amount) * 100 : 0;?>

                            <div class="progress_course">
                                <div class="progress_bar">
                                    <div class="completed_line" style="width:<?=ceil($progress);?>%;<?php if(ceil($progress) == 100) echo ' background:#4BD96A';?>"> </div>
                                </div>
                            </div>

                            <div class="progress-row">
                                <div class="progress-left"><?=ceil($progress);?><?=System::Lang('PERCENTAGE_PASSED');?></div>
                                <div class="progress-right"><?=$amount.System::addTermination($amount, ' урок[TRMNT]');?></div>
                            </div>

                            <?php if(ceil($progress) != 100):?>
                                <div class="current-course-buttom">
                                    <span class="btn-yellow-border"><?=System::Lang('PASSING_PROCESS');?></span>
                                </div>
                        <?php endif;
                    endif;?>

                        <?php /*
                        <?=$access;?>
                        <div class="current-course-bottom">
                            <div class="current-course-price">0000 р.</div>

                            <?php if(!$is_auth):?>
                                <div class="current-course-buttom">
                                    <span class="btn-yellow-border">В процессе прохождения</span>
                                </div>
                            <?php else:?>
                                <div class="current-course-buttom">
                                    <a class="btn-blue" href="#">Получить курс</a>
                                </div>

                                <div class="current-course-lp">
                                    <a href="#" target="_blank">Подробнее о курсе</a>
                                </div>
                            <?php endif;?>
                        </div>
                        */?>
                    </div>
                </div>

                <?php if($sidebar):
                    $widget_arr = $sidebar;
                    require(ROOT . '/template/'.$setting['template'].'/widgets/widget_wrapper.php');
                endif;?>

                <?php //require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
            </aside>
        </div>
    </div>
</div>

<?php
require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');?>

<?php if(!empty($lesson['video_urls'])):?>
    <script src="/template/<?=$setting['template'];?>/js/player_bm.js" type="text/javascript"></script>

    <?php $count = 0;
    $video_url = explode("\r\n", $lesson['video_urls']);
    foreach($video_url as $url):
        if (empty($url)) {
            continue;
        } else {
            $url = base64_encode(trim($url));
        };?>

        <script>
            var player = new Playerjs({id:"bm_player<?=$count++;?>", file:window.atob("<?=$url; ?>"), design:1});
        </script>

    <?php endforeach;
endif;


if(!empty($lesson['audio_urls'])):?>
    <script src="/template/<?=$setting['template'];?>/js/audio_play.js" type="text/javascript"></script>

    <?php $count = 0;
    $audio_url = explode("\r\n", $lesson['audio_urls']);
    foreach($audio_url as $a_url):
        if(empty($a_url)) {
            continue;
        }

        $a_url = base64_encode(trim($a_url));?>

        <script>
          var player = new Playerjs({id:"a_player<?=$count++;?>", file:window.atob("<?=$a_url; ?>"), design:1});
        </script>

    <?php endforeach;
endif;?>
</body>
</html>