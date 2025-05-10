<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>

    <div id="content">
        <div class="layout" id="lk">
            <ul class="breadcrumbs">
                <li><a href="<?=$setting['script_url'];?>/lk/answers"><?=System::Lang('QUESTIONS_LIST');?></a></li>
            </ul>
            
            <div class="content-wrap">
                <div class="maincol<?php if($sidebar) echo '_min';?> content-with-sidebar">
                    <h1><?=System::Lang('CURATOR_OFFICE');?></h1>
                    
                    <div class="userbox dialog">
                        <div class="task_info">
                            <strong><?=System::Lang('TASK_QUESTION');?></strong> <?=$lesson_data['task'];?></div>

                            <?php if($dialog_list):
                                foreach($dialog_list as $item):?>
                                    <div class="dialog_item">
                                        <?php $student = User::getUserNameByID($item['user_id']);?>
                                        <div class="dialog_item__left">
                                            <img src="<?=User::getAvatarUrl($student, $setting);?>" alt="" />
                                        </div>

                                        <div class="dialog_item__right">
                                            <h5 class="dialog_item__title"><?=System::Lang('LESSON_DIALOGUE');?> <a target="_blank" href="<?=$setting['script_url'];?>/courses/<?=$course['alias'];?>/<?=$lesson_data['alias'];?>"><?=$lesson_data['name'];?></a></h5>
                                            <div class="dialog_user_name">
                                                <span class="user_name__link"><?=$student ? $student['user_name'] : 'User Deleted'?>, </span>
                                                <span class="small"># <?=$item['id'];?>, </span>
                                                <span class="small"><?=date("d.m.Y H:i:s", $item['date']);?></span>
                                            </div>

                                            <div class="user_message">
                                                <?=$item['body'];?>
                                            </div>

                                            <?php if (!empty($item['attach'])):?>
                                                <div class="attach mt-5">
                                                    <div>
                                                        <span class="small"><?=System::Lang('ATTACHED_ATTACHMENTS');?></span>
                                                    </div>
                                                    <div>
                                                    <?php foreach(json_decode($item['attach'], true) as $attach):?>
                                                        <a style="margin-right:10px;" target="_blank" href="<?=urldecode($attach['path']);?>">
                                                            <nobr><img src="/template/admin/images/attachment.png" alt="" style="width:16px;margin-right:5px;">
                                                            <span><?=$attach['name'];?></span></nobr>
                                                        </a>
                                                    <?php endforeach?>
                                                    </div>
                                                </div>
                                            <?php endif?>
                                        </div>

                                        <div class="dialog_del_mess">
                                            <a href="<?=$setting['script_url'];?>/lk/answers/delmess/<?=$item['id'];?>"><span class="icon-remove"></span></a>
                                        </div>
                                    </div>

                                    <?php $answers = Course::getAnswerFromMess($item['id']);
                                    if($answers):
                                        foreach($answers as $answer):
                                            $curator = User::getUserNameByID($answer['user_id']);?>
                                            <div class="dialog_item dialog_item__answer"> 
                                                <div class="dialog_item__left">
                                                    <img src="<?=User::getAvatarUrl($curator, $setting);?>" alt="" />
                                                </div>

                                                <div class="dialog_item__right">
                                                    <div class="dialog_user_name">
                                                        <span class="dialog-answer_user_name"><?=$curator['user_name'];?>,</span>
                                                        <span class="small"># <?=$answer['id'];?>, </span>
                                                        <span class="small"><?=date("d.m.Y H:i:s", $answer['date']);?></span>
                                                    </div>

                                                    <div class="user_message">
                                                        <?=$answer['body'];?>
                                                    </div>

                                                    <?php if(!empty($answer['attach'])):?>
                                                        <div class="attach mt-5">
                                                            <div>
                                                                <span class="small"><?=System::Lang('ATTACHED_ATTACHMENTS');?></span>
                                                            </div>

                                                            <div>
                                                                <?php foreach(json_decode($answer['attach'], true) as $attach):?>
                                                                    <a style="margin-right:10px;" target="_blank" href="<?=urldecode($attach['path']);?>">
                                                                        <nobr><img src="/template/admin/images/attachment.png" alt="" style="width:16px;margin-right:5px;">
                                                                        <span><?=$attach['name'];?></span></nobr>
                                                                    </a>
                                                                <?php endforeach?>
                                                            </div>
                                                        </div>
                                                    <?php endif?>
                                                </div>

                                                <div class="dialog_del_mess">
                                                    <a href="<?=$setting['script_url'];?>/lk/answers/delmess/<?=$answer['id'];?>"><span class="icon-remove"></span></a>
                                                </div>
                                            </div>
                                        <?php endforeach;
                                    endif;
                                endforeach;?>

                                <div class="message_box">
                                    <?php if(!$less_status):?>
                                        <form action="" method="POST">
                                            <div class="task-accepted">
                                                <label>
                                                    <input type="hidden" name="success" value="1">
                                                    <input type="submit" value="Принять" class="noaccepted" name="post_message">
                                                </label>
                                            </div>
                                        </form>
                                    <?php else:?>
                                        <div class="task-accepted">
                                            <label>
                                                <span class="accepted"><i class="icon-check"></i> <?=System::Lang('ACCEPTED');?></span>
                                            </label>
                                        </div>
                                    <?php endif;?>

                                    <form enctype="multipart/form-data" action="" method="POST">
                                        <div class="textarea__row">
                                            <div class="textarea__left">
                                                <textarea name="message" class="editor"></textarea>
                                                <div class="attach">
                                                    <label><?=System::Lang('ADD_FILES');?></label>
                                                    <input type="file" multiple name="lesson_attach[]">
                                                </div>
                                            </div>
                                            <div class="textarea__righr">
                                                <input type="submit" value="Ответить" class="button btn-yellow save" name="post_message">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <aside class="sidebar">
                        <div class="filter">
                            <form action="" method="POST">
                                <h4><?=System::Lang('FILTER');?></h4>
                                <div class="one-filter">
                                    <h5 class="one-filter__title"><?=System::Lang('COURSE');?></h5>
                                    <div class="select-wrap">
                                        <select name="course_id">
                                            <?php $course_list = Course::getCourseListFromSitemap();
                                            if($course_list):
                                                foreach($course_list as $course):?>
                                                    <option value="<?=$course['course_id'];?>"<?php if(isset($_SESSION['course_id']) && $_SESSION['course_id'] == $course['course_id']) echo ' selected="selected"';?>><?=$course['name'];?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-button">
                                    <input class="btn-green-small" type="submit" value="Выбрать" name="filter">
                                    <input class="link-red" type="submit" value="Сбросить" name="reset">
                                </div>
                            </form>
                        </div>

                        <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar2.php');?>
                    </aside>
                </div>
            </div>
        </div>
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>

    <script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
    </script>
</body>
</html>